<script setup>
import { ref, computed } from 'vue'

const props = defineProps({
    model: Object
})

const isOpen = ref(false)
const isFolder = computed(() => {
    //if model has array in it, it is a folder
    for (const key in props.model) {
        if (props.model.hasOwnProperty(key)) {
            if (Array.isArray(props.model[key])) {
                return true
            }
        }
    }

})

function toggle() {
    isOpen.value = !isOpen.value
}

function changeType() {
    if (!isFolder.value) {
        props.model.children = []
        isOpen.value = true
    }
}

</script>

<template>
    <li>
        <div
            :class="{ bold: isFolder }"
            @click="toggle"
            @dblclick="changeType">
            {{ model.name }}
            <span v-if="isFolder">[{{ isOpen ? '-' : '+' }}]</span>
        </div>
        <ul v-show="isOpen" v-if="isFolder">
            <!--
              A component can recursively render itself using its
              "name" option (inferred from filename if using SFC)
            -->
            <TreeItem
                class="item"
                v-for="model in model.tables"
                :model="model">
            </TreeItem>
        </ul>
    </li>
</template>
