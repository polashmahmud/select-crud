<script setup>
import {ref, onMounted} from "vue";
import axios from "axios";
import {ElMessage} from 'element-plus'
import _debounce from "lodash.debounce"
import _omitBy from "lodash.omitby"
import _isEmpty from "lodash.isempty"

const props = defineProps({
    url: {
        type: String,
        required: true
    }
})

const data = ref(null)
const selectedValue = ref(null)
const loading = ref(false)
const addLoading = ref(false)
const updateLoading = ref(false)
const isAdding = ref(false)
const isEditing = ref(false)
const editData = ref(null)
const name = ref('')

onMounted(() => {
    getData()
})

const getData = (search = '') => {
    loading.value = true

    axios.get(props.url, {
        params: _omitBy({search}, _isEmpty)
    })
        .then(response => {
            data.value = response.data
        })
        .finally(() => {
            loading.value = false
        })
}

const store = () => {
    addLoading.value = true

    axios.post(props.url, {name: name.value})
        .then(response => {
            data.value.unshift(response.data)
            selectedValue.value = response.data.id
            clear()
        })
        .catch(error => {
            // error show
        })
        .finally(() => {
            addLoading.value = false
        })

}

const clear = () => {
    name.value = ''
    loading.value = false
    addLoading.value = false
    isAdding.value = false
    isEditing.value = false
}

const handleDeleteCancel = () => {
    ElMessage({
        type: 'info',
        message: 'Delete canceled'
    })
}

const handleDelete = () => {
    axios.delete(`${props.url}/${selectedValue.value}`)
        .then(() => {
            data.value = data.value.filter(item => item.id !== selectedValue.value)
            selectedValue.value = null
            ElMessage({
                type: 'success',
                message: 'Deleted Successfully'
            })
        })
}

const update = () => {
    updateLoading.value = true

    axios.put(`${props.url}/${editData.value.id}`, {name: name.value})
        .then(response => {
            let index = data.value.findIndex(item => item.id === editData.value.id)
            data.value[index] = response.data
            clear()
            ElMessage({
                type: 'success',
                message: 'Updated Successfully'
            })
        })
        .finally(() => {
            updateLoading.value = false
        })
}

const handleEditing = () => {
    let selectedData = data.value.find(item => item.id === selectedValue.value)

    editData.value = selectedData
    name.value = selectedData.name
    isEditing.value = true
}

const remoteMethod = _debounce((search) => {
    getData(search)
}, 500)

</script>

<template>
    <div>
        <div v-if="isEditing">
            <el-input
                v-model="name"
                style="max-width: 100%"
                placeholder="Enter name"
                size="large"
            >
                <template #append>
                    <el-button type="primary" @click.prevent="update" :loading="updateLoading">Update</el-button>
                </template>
            </el-input>
            <div class="flex items-center justify-end mt-1">
                <el-button size="small" link type="danger" @click="clear">Cancel</el-button>
            </div>
        </div>
        <div v-else>
            <el-select
                v-model="selectedValue"
                placeholder="Select"
                size="large"
                style="width: 100%"
                :loading="loading"
                clearable
                filterable
                remote
                :remote-method="remoteMethod"
            >
                <template #footer>
                    <el-button v-if="!isAdding" text bg size="small" @click="isAdding = true">
                        Add an option
                    </el-button>
                    <template v-else>
                        <el-input
                            v-model="name"
                            class="option-input mb-3"
                            placeholder="input option name"
                            size="small"
                        />
                        <el-button type="primary" size="small" @click="store" :loading="addLoading">
                            save
                        </el-button>
                        <el-button size="small" @click="clear">cancel</el-button>
                    </template>
                </template>

                <el-option
                    v-for="item in data"
                    :key="item.id"
                    :label="item.name"
                    :value="item.id"
                />
            </el-select>
            <div v-if="selectedValue" class="flex items-center justify-end mt-1">
                <el-button link size="small" type="primary" @click="handleEditing">Edit</el-button>
                <el-popconfirm
                    title="Are you sure to delete this?"
                    @cancel="handleDeleteCancel"
                    @confirm="handleDelete"
                >
                    <template #reference>
                        <el-button link size="small" type="danger">Delete</el-button>
                    </template>
                </el-popconfirm>
            </div>
        </div>
    </div>

</template>
