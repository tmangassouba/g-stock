<template>
    <card-component :title=" editMode ? 'Modifier devise' : 'Ajouter devise' "  @close="$emit('close')">
        <form @submit.prevent="editMode ? editData() : addData()" v-if="form">
            <b-field
                horizontal 
                label="Nom"
                :type="$page.errors.name ? 'is-danger' : ''"
                :message="$page.errors.name ? $page.errors.name[0] : ''"
                class="field-label is-small">
                <b-input name="name" v-model="form.name" size="is-small" required expanded ></b-input>
            </b-field>

            <b-field 
                horizontal 
                label="symbole" 
                class="field-label is-small"
                :type="$page.errors.symbole ? 'is-danger' : ''"
                :message="$page.errors.symbole ? $page.errors.symbole[0] : ''">
                <b-input name="symbole" v-model="form.symbole" size="is-small" required expanded></b-input>
            </b-field>

            <b-field horizontal class="field-label is-small">
                <div style="text-align:right">
                    <b-button size="is-small" type="is-info" native-type="submit" :loading="savingData">{{ editMode ? 'Modifier' : 'Ajouter'}}</b-button>
                    <b-button size="is-small" @click="$emit('close')">Annuler</b-button>
                </div>
            </b-field>
        </form>
    </card-component>
</template>

<script>
    import { CardComponent } from "../Card";

    export default {
        name: 'DeviseForm',
        props:{
            devise: {
                type: Object,
                required: false,
                // default: {}
            }
        },
        components: { CardComponent },
        data() {
            return {
                savingData: false,
            }
        },
        computed: {
            editMode: function() {
                return this.devise && this.devise.id
            },
            form: function() {
                if (this.devise && this.devise.id) {
                    return {
                        id: this.devise.id,
                        name: this.devise.name,
                        symbole: this.devise.symbole
                    }
                }
                return {
                    id: null,
                    name: null,
                    symbole: null
                }
            }
        },
        methods: {
            addData() {
                this.savingData = true
                this.$inertia.post('/devises', this.form)
                .then(() => {
                    if (this.$page.flash.message != null ) {
                        // this.resetForm()
                        this.$emit('resetData')
                        this.$buefy.notification.open({
                            message: 'Devise ajoutée avec succès.',
                            type: 'is-success'
                        })
                    }
                }).finally(() => {
                    this.savingData = false
                })
            },
            editData() {
                this.savingData = true
                this.$inertia.put('/devises/' + this.form.id, this.form)
                .then(() => {
                    if (this.$page.flash.message != null ) {
                        this.$buefy.notification.open({
                            message: 'Devise modifiée avec succès.',
                            type: 'is-success'
                        })
                    }
                })
                .catch(({message}) => {
                    // this.$handleMessage(message, 'danger');
                }).finally(() => {
                    this.savingData = false
                })
            }
        },
        created() {
            //
        },
    }
</script>
