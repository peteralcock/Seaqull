<script setup lang="ts">
import {Database} from "@/types";

defineProps<{
    database: Database;
}>();
</script>
<template>
  <div class="bg-white overflow-clip shadow-xl sm:rounded-lg">
    <h3 class="gap-3 px-6 py-3">Database Schema</h3>
    <div v-for="table in database.tables" :key="table.id"
         class="gap-3 px-6 py-3 border-spacing-1 border-gray-100 border-2">
                        <span>{{table.name}}:
                            {
                            <span v-for="column in table.columns" :key="column.id" class="pl-3 my-1 mx-1">
                                <span>{{column.name}}</span>:<span
                                class="font-mono font-semibold">{{column.type}}</span><br/>
                            </span>
                            }
                        </span>
    </div>
  </div>
</template>
