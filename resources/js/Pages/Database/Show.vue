<script setup lang="ts">
import {Database, SavedReport} from "@/types";
import AppLayout from "@/Layouts/AppLayout.vue";
import DatabaseSchema from "@/Pages/Database/DatabaseSchema.vue";
import TextInput from "@/Components/TextInput.vue";
import {ref} from "vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import InputLabel from "@/Components/InputLabel.vue";
import {router, useForm, usePage} from "@inertiajs/vue3";
import axios from "axios";


const gptResponse = ref({
    data: "",
    query: "",
    success: false,
});

const gptError = ref({
    error: false,
});

const prompt = ref("");
const saveButton = ref(false);


const props = defineProps<{
    database: Database;
    reports: SavedReport[];
}>();

const submit = () => {
    axios.post(route('database.ask-to-gpt', {database: props.database.id}), {
        prompt: prompt.value,
    }).then((response) => {
        console.log(response.data)
        gptResponse.value.data = response.data.data;
        gptResponse.value.query = response.data.query;
        gptResponse.value.success = true;
        saveButton.value = true;
        //prompt.value = "";
    }).catch((error) => {
        console.log(error);
        gptError.value.error = true;
    });
};

const saveQuery = () => {
    axios.AxiosHeaders['Accept'] = 'application/html';
    axios.post(route('database.save-query', {database: props.database.id}), {
        prompt: prompt.value,
        query: gptResponse.value.query,
        response: gptResponse.value.data,
    }).then((response) => {
        //console.log(response.data)
        //re-render the page
        //prompt.value = "";
        router.visit(route('database.show', {database: props.database.id}), {}, {only: ['reports']})
        saveButton.value = false;
    }).catch((error) => {
        console.log(error);
    });
}
</script>

<template>
    <AppLayout title="Dashboard">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Database Name: {{ database.name }}
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-12xl mx-auto sm:px-6 lg:px-8 grid grid-cols-4 gap-2">
                <div class="bg-white overflow-clip shadow-xl sm:rounded-lg">
                    <h3 class="gap-3 px-6 py-3">History</h3>
                    <div v-for="report in reports" :key="report.id">
                        <div class="gap-3 px-6 py-3 border-spacing-1 border-gray-100 border-2">
                            <div class="flex justify-between">
                                <span class="text-sm">{{ report.prompt }}</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-span-2 bg-white overflow-clip shadow-xl sm:rounded-lg">
                    <!-- big text input to enter chat-gpt prompt -->
                    <h3 class="gap-3 px-6 py-3">Chat</h3>
                    <form @submit.prevent="submit">
                        <div class=" m-3">
                            <InputLabel>Prompt:</InputLabel>
                            <TextInput
                                label="Prompt"
                                name="prompt"
                                aria-placeholder="Enter prompt"
                                placeholder="What is bet selling product in last 5 weeks?"
                                class="w-full"
                                required
                                v-model="prompt"
                            />
                        </div>
                        <PrimaryButton class="mx-3">Submit</PrimaryButton>
                    </form>

                    <div v-if="gptResponse.success" class="gap-6 px-6 py-3">
                        Query:
                        <div class="shadow-xl sm:rounded-lg bg-black text-white p-2 mb-4"
                             v-html="gptResponse.query"></div>
                        <PrimaryButton :disabled="!saveButton" @click="saveQuery">Save query</PrimaryButton>
                        Response:
                        <div class="shadow-xl sm:rounded-lg p-2 mb-4" v-html="gptResponse.data"></div>
                    </div>
                    <div v-if="gptError.error" class="gap-6 px-6 py-3">
                        <div class="shadow-xl sm:rounded-lg bg-red-500 text-white p-2 mb-4">Error in GPT</div>
                    </div>
                </div>
                <DatabaseSchema :database="database"/>
            </div>
        </div>
    </AppLayout>
</template>

<style scoped>
.item {
    cursor: pointer;
    line-height: 1.5;
}

.bold {
    font-weight: bold;
}
</style>
