<template>
    <card-component :title=" 'Télécharger des fichiers' "  @close="$emit('close-file-modal')">
        <form @submit.prevent="uploadFile">
            <div class="columns">
                <div class="column is-5">
                    <b-upload drag-drop expanded :loading="uploadingFile" @input="changeImage">
                        <!-- v-model="files"  -->
                        <section class="section">
                            <div class="content has-text-centered">
                                <p>
                                    <b-icon icon="upload" size="is-large"></b-icon>
                                </p>
                            </div>
                        </section>
                    </b-upload>
                </div>
                <div class="column is-7">
                    <div class="tags">
                        <span class="tag is-info" v-if="file">
                            {{file.name}}
                            <button class="delete is-small" type="button" @click="dropFile()"></button>
                        </span>
                    </div>
                    <span v-if="$page.errors.file" class="has-text-danger">{{ $page.errors.file[0] }}</span>
                    <div v-if="message" class="has-text-success">{{ message }}</div>
                </div>
            </div>
            
            <div class="">
                <b-button size="is-small" type="is-info" native-type="submit" :loading="uploadingFile" :disabled="file == null">Télécharger</b-button>
                <b-button size="is-small" @click="$emit('close-file-modal')">Annuler</b-button>
            </div>
        </form>
    </card-component>
</template>

<script>
    import { Inertia } from '@inertiajs/inertia'
    import { CardComponent } from "../Card";

    export default {
        name: 'FileForm',
        props:{
            operation: {
                type: Object,
                required: true,
                // default: {}
            }
        },
        components: { CardComponent },
        data() {
            return {
                file: null,
                uploadingFile: false,
                message: null
            }
        },

        methods: {
            changeImage(file) {
                this.file = file
                this.message = null
            },
            uploadFile() {
                let data = new FormData()
                data.append('file', this.file || '')
                this.uploadingFile = true
                Inertia.post('/operations/' + this.operation.reference + '/upload-files', data)
                .then(() => {
                    if (this.$page.flash.message != null ) {
                        this.file = null
                        this.message = "Ajouté avec succès."
                        this.$emit('reloadDocs')
                    }
                }).finally(() => {
                    this.uploadingFile = false
                })
            },
            dropFile() {
                this.file = null
            }
        },
    }
</script>