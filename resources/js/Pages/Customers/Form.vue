<template>
    <app-layout>
        <title-bar :title-stack="titleStack">
            <div class="buttons is-right">
                <span v-if="editMode">
                    <b-button class="is-small" icon-left="arrow-left" tag="a" :href="'/clients/' + customer.code">Annuler</b-button>
                    <b-button class="is-danger is-small" icon-left="delete-outline" @click="deleteCustomer">Supprimer</b-button>
                </span>
                <b-button class="is-small" icon-left="arrow-left" tag="a" href="/clients" v-else>Annuler</b-button>
            </div>
        </title-bar>

        <section class="section is-main-section">
            <form @submit.prevent="editMode ? updateData() : addData()" v-if="form">
                <!-- {{ form }} -->
                <div class="columns">
                    <div class="column">
                        
                        <b-field
                            horizontal 
                            label="Type"
                            :type="$page.errors.type ? 'is-danger' : ''"
                            :message="$page.errors.type ? $page.errors.type[0] : ''"
                            class="field-label is-small">
                            <div>
                                <b-radio name="type" v-model="form.type" :native-value="'Entreprise'" type="is-info"> Entreprise </b-radio>
                                <b-radio name="type" v-model="form.type" :native-value="'Particulier'" type="is-info"> Particulier </b-radio>
                            </div>
                        </b-field>
                        
                        <b-field
                            horizontal 
                            label="Titre"
                            :type="$page.errors.title ? 'is-danger' : ''"
                            :message="$page.errors.title ? $page.errors.title[0] : ''"
                            class="field-label is-small">
                            <b-select placeholder="Titre" name="title" v-model="form.title" size="is-small" required expanded>
                                <option value="M.">M.</option>
                                <option value="Mme.">Mme.</option>
                                <option value="Mlle.">Mlle.</option>
                                <option value="Dr.">Dr.</option>
                            </b-select>
                        </b-field>

                        <b-field
                            horizontal 
                            label="Prénom"
                            :type="$page.errors.fisrt_name ? 'is-danger' : ''"
                            :message="$page.errors.fisrt_name ? $page.errors.fisrt_name[0] : ''"
                            class="field-label is-small">
                            <b-input name="fisrt_name" v-model="form.fisrt_name" size="is-small" required expanded></b-input>
                        </b-field>

                        <b-field
                            horizontal 
                            label="Nom"
                            :type="$page.errors.last_name ? 'is-danger' : ''"
                            :message="$page.errors.last_name ? $page.errors.last_name[0] : ''"
                            class="field-label is-small">
                            <b-input name="last_name" v-model="form.last_name" size="is-small" required expanded></b-input>
                        </b-field>

                        <b-field
                            horizontal 
                            label="Adresse"
                            :type="$page.errors.address || $page.errors.city ? 'is-danger' : ''"
                            :message="$page.errors.address ? $page.errors.address[0] : $page.errors.city ? $page.errors.city[0] : ''"
                            class="field-label is-small">
                            <b-input name="address" v-model="form.address" placeholder="Adresse" size="is-small" expanded></b-input>
                            <b-input name="city" v-model="form.city" placeholder="Ville" size="is-small" expanded></b-input>
                        </b-field>

                        <b-field
                            horizontal 
                            label="Téléphone"
                            :type="$page.errors.telephone || $page.errors.mobile ? 'is-danger' : ''"
                            :message="$page.errors.telephone ? $page.errors.telephone[0] : $page.errors.mobile ? $page.errors.mobile[0] : ''"
                            class="field-label is-small">
                            <b-input name="telephone" v-model="form.telephone" size="is-small" expanded></b-input>
                            <!-- <b-input name="mobile" v-model="form.mobile" placeholder="Mobile" size="is-small" expanded></b-input> -->
                        </b-field>

                        <b-field
                            horizontal 
                            label="Email"
                            :type="$page.errors.email ? 'is-danger' : ''"
                            :message="$page.errors.email ? $page.errors.email[0] : ''"
                            class="field-label is-small">
                            <b-input name="email" v-model="form.email" type="email" size="is-small" expanded></b-input>
                        </b-field>

                        <b-field
                            horizontal 
                            label="Site web"
                            :type="$page.errors.website ? 'is-danger' : ''"
                            :message="$page.errors.website ? $page.errors.website[0] : ''"
                            class="field-label is-small">
                            <b-input name="website" v-model="form.website" type="url" placeholder="http://www.exemple.com" size="is-small" expanded></b-input>
                        </b-field>

                        <b-field
                            horizontal 
                            label="Compagnie"
                            :type="$page.errors.company ? 'is-danger' : ''"
                            :message="$page.errors.company ? $page.errors.company[0] : ''"
                            class="field-label is-small">
                            <b-input name="company" v-model="form.company" size="is-small" expanded></b-input>
                        </b-field>

                    </div>

                    <div class="column"></div>
                </div>

                <hr>
                <b-field>
                    <b-button size="is-small" type="is-info" native-type="submit" :loading="isSaving">{{ title }}</b-button>
                </b-field>
            </form>
        </section>
    </app-layout>
</template>

<script>
    import { Inertia } from '@inertiajs/inertia'
    import AppLayout from '../../Layouts/AppLayout'
    import TitleBar from '../../Menu/TitleBar'

    export default {
        props: ['customer', 'message', 'errors'],
        components: {
            AppLayout,
            TitleBar,
        },
        data() {
            return {
                isSaving: false,
                checkedRows: [],
                form: {
                    id: null,
                    code: null,
                    title: null,
                    fisrt_name: null,
                    last_name: null,
                    city: null,
                    telephone: null,
                    // mobile: null,
                    email: null,
                    website: null,
                    company: null
                }
            }
        },

        methods: {
            addData() {
                this.isSaving = true
                this.$inertia.post('/clients', this.form)
                .then(() => {
                    if (this.$page.flash.message != null ) {
                        this.$buefy.notification.open({
                            message: 'Ajoutée avec succès.',
                            type: 'is-success'
                        })
                    }
                })
                .catch(({message}) => {
                    // this.$handleMessage(message, 'danger');
                }).finally(() => {
                    this.isSaving = false
                })
            },
            updateData() {
                this.isSaving = true
                this.$inertia.put('/clients/' + this.customer.code, this.form)
                .then(() => {
                    if (this.$page.flash.message != null ) {
                        this.$buefy.notification.open({
                            message: 'Modifiée avec succès.',
                            type: 'is-success'
                        })
                    }
                })
                .catch(({message}) => {
                    // this.$handleMessage(message, 'danger');
                }).finally(() => {
                    this.isSaving = false
                })
            },
            deleteCustomer() {
                if (this.checkedRows.length) {
                    this.$buefy.dialog.confirm({
                        title: 'Supprimer clients',
                        message: 'Etes-vous sûrs de vouloir <b>supprimer</b> ce client ?<br/> Cette action ne peut pas être annulée.',
                        confirmText: 'Supprimer client(s)',
                        type: 'is-danger',
                        hasIcon: true,
                        size: 'is-small',
                        onConfirm: () => {
                            // this.$buefy.toast.open('Account deleted!')
                            this.isDeleting = true
                            var checkedForm = {
                                checkedRows: this.checkedRows
                            }
                            this.$inertia.post('/clients/delete-clients', checkedForm)
                            .then(() => {
                                if (this.$page.flash.message != null ) {
                                    // this.resetForm()
                                    this.$buefy.notification.open({
                                        message: 'Client supprimé avec succès.',
                                        type: 'is-success'
                                    })
                                }
                            })
                            .catch(({message}) => {
                                // this.$handleMessage(message, 'danger');
                            }).finally(() => {
                                this.isDeleting = false
                            })
                        }
                    })
                }
            }
        },

        computed: {
            title() {
                return this.editMode ? 'Modifier' : 'Ajouter'
            },
            titleStack () {
                return [this.title + ' client']
            },
            editMode() {
                return this.customer && this.customer.id ? true : false;
            },
        },

        created() {
            if (this.editMode) {
                this.form.id         = this.customer.id,
                this.form.code       = this.customer.code,
                this.form.type       = this.customer.type,
                this.form.title      = this.customer.title,
                this.form.fisrt_name = this.customer.fisrt_name,
                this.form.last_name  = this.customer.last_name,
                this.form.address    = this.customer.address,
                this.form.city       = this.customer.city,
                this.form.telephone  = this.customer.telephone,
                // this.form.mobile     = this.customer.mobile,
                this.form.email      = this.customer.email,
                this.form.website    = this.customer.website,
                this.form.company    = this.customer.company
            }
            if (this.customer) {
                this.checkedRows[0] = this.customer
            }
        },
    }
</script>