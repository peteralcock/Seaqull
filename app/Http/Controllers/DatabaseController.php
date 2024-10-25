<?php

namespace App\Http\Controllers;


use App\Helpers\ChatGPT;
use App\Http\Requests\DatabaseRequest;
use App\Models\Column;
use App\Models\Database;
use App\Models\Table;
use Config;
use DB;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Request;

class DatabaseController extends Controller
{
    public function store(DatabaseRequest $request)
    {
        $validated = $request->validated();
        $validated['user_id'] = auth()->user()->id;
        $validated['team_id'] = auth()->user()->currentTeam->id;
        $database = Database::create($validated);

        return back()->with(['status' => true, 'message' => 'Database Created Successfully']);
    }

    public function testConnection(DatabaseRequest $request)
    {

        $validated = $request->validated();
        //Temporary edit "secondary_mysql" database configurations
        Config::set('database.connections.secondary_mysql.host', $validated['host']);
        Config::set('database.connections.secondary_mysql.port', $validated['port']);
        Config::set('database.connections.secondary_mysql.username', $validated['username']);
        Config::set('database.connections.secondary_mysql.password', $validated['password']);
        Config::set('database.connections.secondary_mysql.database', $validated['name']);

        $connection = DB::connection('secondary_mysql');
        //Check if the database details are correct by trying to connect to the database
        try {
            //If the connection is successful, return a success message
            $connection->getPdo();
        } catch (\Exception $e) {
            //If the connection is unsuccessful, return an error message
            //flash message
            return back()->with(['status' => false, 'message' => $e->getMessage()]);

        }

        return back()->with(['status' => true, 'message' => 'Connection Successful']);
    }

    public function show(Database $database)
    {

        //TODO: move this to a middleware
        Config::set('database.connections.secondary_mysql.host', $database->host);
        Config::set('database.connections.secondary_mysql.port', $database->port);
        Config::set('database.connections.secondary_mysql.username', $database->username);
        Config::set('database.connections.secondary_mysql.password', $database->password);
        Config::set('database.connections.secondary_mysql.database', $database->name);

        //TODO: move this to somewhere else, it should not try to fetch the tables and columns every time
        $tables = DB::connection('secondary_mysql')->select("SELECT TABLE_NAME as 'table_name'FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_SCHEMA = '$database->name'");
        foreach ($tables as $element) {
            $table = Table::firstOrCreate(
                [
                    'name' => $element->table_name,
                    'database_id' => $database->id,
                ],
                [
                    'name' => $element->table_name,
                    'database_id' => $database->id,
                ]);


            $columns = DB::connection('secondary_mysql')->select("SELECT COLUMN_NAME AS `column_name`,DATA_TYPE AS `data_type`,CHARACTER_SET_NAME AS `character_set_name`
                                                                                FROM
                                                                                    INFORMATION_SCHEMA.COLUMNS
                                                                                WHERE
                                                                                    TABLE_SCHEMA = '$database->name'
                                                                                    AND TABLE_NAME = '$table->name';");
            foreach ($columns as $subElement) {
                $column = Column::firstOrCreate(
                    [
                        'name' => $subElement->column_name,
                        'table_id' => $table->id,
                    ],
                    [
                        'name' => $subElement->column_name,
                        'table_id' => $table->id,
                        'type' => $subElement->data_type,
                        'character_set' => $subElement->character_set_name,
                    ]);
            }
        }

        return Inertia::render('Database/Show', [
            'database' => $database->with('tables.columns')->first(),
            'reports' => $database->reports()->orderBy('created_at', 'desc')->get(),
        ]);
    }


    public function askToGPT(Database $database, Request $request)
    {
        $validated = $request::validate([
            'prompt' => 'required|string',
        ]);

        //TODO: move this to a middleware
        Config::set('database.connections.secondary_mysql.host', $database->host);
        Config::set('database.connections.secondary_mysql.port', $database->port);
        Config::set('database.connections.secondary_mysql.username', $database->username);
        Config::set('database.connections.secondary_mysql.password', $database->password);
        Config::set('database.connections.secondary_mysql.database', $database->name);

        //database schema
        $databaseSchema = "";
        foreach ($database->tables as $table) {
            $databaseSchema .= "CREATE TABLE $table->name (\n";
            foreach ($table->columns as $column) {
                $databaseSchema .= "\t $column->name $column->type,\n";
            }
            $databaseSchema .= ");\n";
        }


        $response = ChatGPT::ask($databaseSchema, $validated['prompt']);

        if ($response) {
            $sqlResponse = DB::connection('secondary_mysql')->select($response);
            $database->connected_at = now();
            $database->save();

            $responseAsHtml = $this->createTableFromSqlResponse($sqlResponse);


            return response()->json([
                'data' => $responseAsHtml,
                'query' => nl2br($response),
                'prompt' => $validated['prompt'],
            ]);
        } else {
            return response()->json([
                'data' => "",
                'query' => "",
                'prompt' => $validated['prompt'],
            ]);
        }


    }


    public function saveToReport(Database $database, Request $request)
    {
        $validated = $request::validate([
            'prompt' => 'required|string',
            'query' => 'required|string',
        ]);

        $report = $database->reports()->create([
            'prompt' => $validated['prompt'],
            'query' => strip_tags($validated['query']),
        ]);

       return to_route('database.show', $database->id);
    }

    /**
     * @param  array  $sqlResponse
     * @return string
     */
    private function createTableFromSqlResponse(array $sqlResponse): string
    {
        $responseAsHtml = "<table class='table'>";
        $responseAsHtml .= "<thead>";
        $responseAsHtml .= "<tr>";
        foreach ($sqlResponse[0] as $key => $value) {
            $responseAsHtml .= "<th>$key</th>";
        }
        $responseAsHtml .= "</tr>";
        $responseAsHtml .= "</thead>";
        $responseAsHtml .= "<tbody>";
        foreach ($sqlResponse as $row) {
            $responseAsHtml .= "<tr>";
            foreach ($row as $key => $value) {
                $responseAsHtml .= "<td>$value</td>";
            }
            $responseAsHtml .= "</tr>";
        }
        $responseAsHtml .= "</tbody>";
        $responseAsHtml .= "</table>";
        return $responseAsHtml;
    }
}
