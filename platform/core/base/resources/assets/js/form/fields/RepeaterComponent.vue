<template>
    <div class="repeater-group">
        <div class="form-group mb-3" v-for="(item, index) in items">
            <div v-html="item"></div>
            <span class="remove-item-button" type="button" @click="deleteRow(index)"><i class="fa fa-times"></i></span>
        </div>

        <button :class="isAdding ? 'button-loading btn btn-info' : 'btn btn-info'" type="button" @click="addRow">
            {{ __('Add new') }}
        </button>
    </div>
</template>

<script>
export default {
    data: function () {
        return {
            items: [],
            isAdding: false,
        }
    },
    props: {
        fields: {
            type: Array,
            default: () => [],
            required: true,
        },
        added: {
            type: Array,
            default: () => [],
            required: true,
        },
    },

    mounted() {
        if (!this.added.length) {
            this.addRow()
        } else {
            for (const item of this.added) {
                this.items.push(item)
            }
        }
    },

    methods: {
        addRow: function () {
            this.isAdding = true
            for (const item of this.fields) {
                this.items.push(item.replaceAll('__key__', this.items.length))
            }
            this.isAdding = false
        },
        deleteRow: function (index) {
            this.items.splice(index, 1)
        },
        removeSelectedItem: function () {
            for (const item of this.items) {
                this.items.slice(i, 1)
            }
        },
    },
    watch: {
        items(value) {
            if (value) {
                this.$nextTick(() => {
                    if (window.Botble) {
                        window.Botble.initResources()
                        window.Botble.initMediaIntegrate()
                    }

                    if (window.EditorManagement) {
                        new EditorManagement().init()
                    }
                })
            }
        },
    },
}
</script>
