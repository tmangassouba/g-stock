<template>
    <app-layout>
        <title-bar :title-stack="titleStack">
            <div class="buttons is-right">
                <b-button class="is-info is-small" icon-left="plus" @click="addData()">Nouveau</b-button>
                <b-button class="is-danger is-small" icon-left="delete-outline" @click="deleteData" v-if="checkedRows.length">Supprimer</b-button>
            </div>
        </title-bar>

        <section class="section is-main-section">
            <b-table 
                :data="unites"
                :loading="loading"
                striped
                hoverable

                checkable
                :checked-rows.sync="checkedRows"
            >
                <b-table-column field="name" label="Unité" v-slot="props">
                    {{ props.row.name }}
                </b-table-column>

                <b-table-column numeric v-slot="props">
                    <b-button @click="editData(props.row)" size="is-small" icon-left="pencil-outline" type="is-info is-light"></b-button>
                </b-table-column>

                <template slot="empty">
                    <section class="section">
                        <div class="content has-text-grey has-text-centered">
                            <p><b-icon icon="inbox" size="is-large"></b-icon></p>
                            <p>Aucun résultat.</p>
                        </div>
                    </section>
                </template>
            </b-table>
        </section>

        <b-modal 
            v-model="isModalActive"
            trap-focus
            :destroy-on-hide="false"
            :can-cancel="['escape', 'x']"
            :width="640"
        >
            <card-component :title=" editMode ? 'Modifier unité' : 'Ajouter unité' "  @close="isModalActive = false">
                <form @submit.prevent="editMode ? updateData() : saveData()" v-if="form">
                    <b-field
                        horizontal 
                        label="Nom"
                        :type="$page.errors.name ? 'is-danger' : ''"
                        :message="$page.errors.name ? $page.errors.name[0] : ''"
                        class="field-label is-small">
                        <b-input name="name" v-model="form.name" size="is-small" required expanded ></b-input>
                    </b-field>

                    <b-field horizontal class="field-label is-small">
                        <div style="text-align:right">
                            <b-button size="is-small" type="is-info" native-type="submit" :loading="savingData">{{ editMode ? 'Modifier' : 'Ajouter'}}</b-button>
                            <b-button size="is-small" @click="isModalActive = false">Annuler</b-button>
                        </div>
                    </b-field>
                </form>
            </card-component>
        </b-modal>

        <b-notification :closable="false" class="loading-notification">
            <b-loading :is-full-page="isFullPage" v-model="isDeleting" :can-cancel="false"></b-loading>
        </b-notification>
    </app-layout>
</template>

<script>
    import AppLayout from '../../Layouts/AppLayout'
    import TitleBar from '../../Menu/TitleBar'
    import { Inertia } from '@inertiajs/inertia'
    import { CardComponent } from "../../components/Card";

    export default {
        props: ['unites', 'errors'],
        components: {
            AppLayout,
            TitleBar,
            CardComponent
        },
        data() {
            return {
                selectedData: {},
                isModalActive: false,
                loading: false,
                checkedRows: [],

                isFullPage: true,
                isDeleting: false,
                savingData: false,

                form: {
                    id: null,
                    name: null
                }
            }
        },
        
        methods: {
            loadAsyncData() {
                Inertia.visit('/articles', {
                    method: 'get'
                })
            },
            addData() {
                this.form.id = null
                this.form.name = null
                this.isModalActive = true
            },
            saveData() {
                this.savingData = true
                this.$inertia.post('/parametres/unites', this.form)
                .then(() => {
                    if (this.$page.flash.message != null ) {
                        this.form.id = null
                        this.form.name = null
                        this.$buefy.notification.open({
                            message: 'Ajoutée avec succès.',
                            type: 'is-success'
                        })
                    }
                }).finally(() => {
                    this.savingData = false
                })
            },
            editData(data) {
                this.form = data
                this.isModalActive = true
            },
            updateData() {
                this.savingData = true
                this.$inertia.put('/parametres/unites/' + this.form.id, this.form)
                .then(() => {
                    if (this.$page.flash.message != null ) {
                        this.$buefy.notification.open({
                            message: 'Ajoutée avec succès.',
                            type: 'is-success'
                        })
                    }
                }).finally(() => {
                    this.savingData = false
                })
            },
            deleteData() {
                if (this.checkedRows.length) {
                    this.$buefy.dialog.confirm({
                        title: 'Supprimer unités',
                        message: 'Etes-vous sûrs de vouloir <b>supprimer</b> ce(tte)(s) unité(s) ?<br/> Cette action ne peut pas être annulée.',
                        confirmText: 'Supprimer unité(s)',
                        type: 'is-danger',
                        hasIcon: true,
                        size: 'is-small',
                        onConfirm: () => {
                            // this.$buefy.toast.open('Account deleted!')
                            this.isDeleting = true
                            var checkedForm = {
                                checkedRows: this.checkedRows
                            }
                            this.$inertia.post('/parametres/unites/delete-unites', checkedForm)
                            .then(() => {
                                if (this.$page.flash.message != null ) {
                                    // this.resetForm()
                                    this.$buefy.notification.open({
                                        message: 'Unité(s) supprimée(s) avec succès.',
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
        created() {
            // console.log(this.unites)
        },

        computed: {
            titleStack () {
                return ['Articles']
            },
            editMode() {
                return this.form && this.form.id
            }
        },
    }
</script>
