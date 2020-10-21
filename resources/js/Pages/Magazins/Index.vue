<template>
    <app-layout>
        <title-bar :title-stack="titleStack">
            <div class="buttons is-right">
                <b-button class="is-info is-small" icon-left="plus" @click="addMagazin()">Nouveau</b-button>
                <b-button class="is-danger is-small" icon-left="delete-outline" @click="deleteMagazins" v-if="checkedRows.length">Supprimer</b-button>
            </div>
        </title-bar>

        <section class="section is-main-section">
            <b-table 
                :data="magazins.data"
                striped
                hoverable

                checkable
                :checked-rows.sync="checkedRows"
            >
                <!-- :default-sort-direction="defaultSortOrder" -->
                <b-table-column field="name" label="Magazin" v-slot="props">
                    {{ props.row.name }}
                </b-table-column>
                <b-table-column field="adresse" label="Adresse" v-slot="props">
                    {{ props.row.adresse }}
                </b-table-column>
                <b-table-column field="ville" label="Ville" v-slot="props">
                    {{ props.row.ville }}
                </b-table-column>
                <b-table-column field="phone" label="Téléphone" v-slot="props">
                    {{ props.row.phone }}
                </b-table-column>
                <b-table-column field="email" label="Email" v-slot="props">
                    {{ props.row.email }}
                </b-table-column>
                <b-table-column field="actif" label="État" sortable v-slot="props">
                    <b-tag type="is-success is-light" v-if="props.row.actif">Actif</b-tag>
                    <b-tag type="is-danger is-light" v-else>Inactif</b-tag>
                </b-table-column>
                <b-table-column width="40" numeric v-slot="props">
                    <b-button @click="editMagazin(props.row)" size="is-small" icon-left="pencil-outline" type="is-info is-light"></b-button>
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
            <magazin-form :magazin="selectMagazin" @resetData="selectMagazin = {}" @close="isModalActive = false"></magazin-form>
        </b-modal>

        <b-notification :closable="false" class="loading-notification">
            <b-loading :is-full-page="isFullPage" v-model="isDeleting" :can-cancel="false"></b-loading>
        </b-notification>
    </app-layout>
</template>

<script>
    import AppLayout from '../../Layouts/AppLayout'
    import TitleBar from '../../Menu/TitleBar'
    import { MagazinForm } from "../../components/Magazins"
    import { Inertia } from '@inertiajs/inertia'

    export default {
        props: ['magazins', 'message', 'errors'],
        components: {
            AppLayout,
            TitleBar,
            MagazinForm
        },
        data() {
            return {
                selectMagazin: {},
                isModalActive: false,
                loading: false,
                checkedRows: [],

                isFullPage: true,
                isDeleting: false,
            }
        },
        
        methods: {
            addMagazin() {
                this.selectMagazin = {}
                this.isModalActive = true
            },
            editMagazin(magazin) {
                this.selectMagazin = magazin
                this.isModalActive = true
            },
            deleteMagazins() {
                if (this.checkedRows.length) {
                    this.$buefy.dialog.confirm({
                        title: 'Supprimer magazin(s)',
                        message: 'Etes-vous sûrs de vouloir <b>supprimer</b> ce(s) magazin(s) ?<br/> Cette action ne peut pas être annulée.',
                        confirmText: 'Supprimer magazin(s)',
                        type: 'is-danger',
                        hasIcon: true,
                        size: 'is-small',
                        onConfirm: () => {
                            // this.$buefy.toast.open('Account deleted!')
                            this.isDeleting = true
                            var checkedForm = {
                                checkedRows: this.checkedRows
                            }
                            this.$inertia.post('/magazins/delete-magazins', checkedForm)
                            .then(() => {
                                if (this.$page.flash.message != null ) {
                                    this.checkedRows = []
                                    this.$buefy.notification.open({
                                        message: 'Magazin(s) supprimé(s) avec succès.',
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
            //
        },

        computed: {
            // _sortField: function () {
            //     return this.sortField
            // },
            // _sortOrder: function () {
            //     return this.sortOrder
            // },
            titleStack () {
                return ['Magazins']
            }
        },

        // watch: {
        //     checkedData: function () {
        //         this.deleteUsersForm.checkedData = this.checkedData
        //     }
        // },
    }
</script>
