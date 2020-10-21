<template>
    <card-component :title=" editMode ? 'Modifier magazin' : 'Ajouter magazin' "  @close="$emit('close')">
        <form @submit.prevent="editMode ? editData() : addData()" v-if="form">
            <b-field
                horizontal 
                label="Magazin"
                :type="$page.errors.name ? 'is-danger' : ''"
                :message="$page.errors.name ? $page.errors.name[0] : ''"
                class="field-label is-small">
                <b-input name="name" v-model="form.name" size="is-small" required expanded ></b-input>
            </b-field>

            <b-field 
                horizontal 
                label="Adresse" 
                class="field-label is-small"
                :type="$page.errors.adresse ? 'is-danger' : ''"
                :message="$page.errors.adresse ? $page.errors.adresse[0] : ''">
                <b-input name="adresse" v-model="form.adresse" size="is-small" expanded></b-input>
            </b-field>

            <b-field
                horizontal
                label="Ville" 
                class="field-label is-small"
                :type="$page.errors.ville ? 'is-danger' : ''"
                :message="$page.errors.ville ? $page.errors.ville[0] : ''">
                <b-input name="ville" v-model="form.ville" size="is-small" expanded></b-input>
            </b-field>

            <b-field
                horizontal
                label="Email" 
                class="field-label is-small"
                :type="$page.errors.email ? 'is-danger' : ''"
                :message="$page.errors.ville ? $page.errors.email[0] : ''">
                <b-input type="email" name="email" v-model="form.email" size="is-small" expanded></b-input>
            </b-field>

            <b-field 
                horizontal 
                label="Téléphone" 
                class="field-label is-small"
                :type="$page.errors.phone ? 'is-danger' : ''"
                :message="$page.errors.phone ? $page.errors.phone[0] : ''">
                <b-input name="phone" v-model="form.phone" size="is-small" expanded></b-input>
            </b-field>

            <b-field
                horizontal 
                label="Actif" 
                class="field-label is-small"
                :type="$page.errors.actif ? 'is-danger' : ''"
                :message="$page.errors.actif ? $page.errors.actif[0] : ''">
                <b-switch
                    name="actif"
                    v-model="form.actif"
                    :true-value="1"
                    :false-value="0"
                    type="is-success"
                    size="is-small-" ></b-switch>
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
        name: 'MagazinForm',
        props:{
            magazin: {
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
                return this.magazin && this.magazin.id
            },
            form: function() {
                if (this.magazin && this.magazin.id) {
                    return {
                        id: this.magazin.id,
                        name: this.magazin.name,
                        adresse: this.magazin.adresse,
                        ville: this.magazin.ville,
                        phone: this.magazin.phone,
                        email: this.magazin.email,
                        actif: this.magazin.actif
                    }
                }
                return {
                    id: null,
                    name: null,
                    adresse: null,
                    ville: null,
                    phone: null,
                    email: null,
                    actif: 1
                }
            }
        },
        methods: {
            addData() {
                this.savingData = true
                this.$inertia.post('/magazins', this.form)
                .then(() => {
                    if (this.$page.flash.message != null ) {
                        // this.resetForm()
                        this.$emit('resetData')
                        this.$buefy.notification.open({
                            message: 'Magazin ajouté avec succès.',
                            type: 'is-success'
                        })
                    }
                }).finally(() => {
                    this.savingData = false
                })
            },
            editData() {
                this.savingData = true
                this.$inertia.put('/magazins/' + this.form.id, this.form)
                .then(() => {
                    if (this.$page.flash.message != null ) {
                        this.$buefy.notification.open({
                            message: 'Magazin modifié avec succès.',
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
