<template>
    <app-layout>
        <title-bar :title-stack="titleStack">
            <div class="buttons is-right">
                <b-button class="is-info is-small" icon-left="plus" @click="addDevise()">Nouveau</b-button>
                <b-button class="is-danger is-small" icon-left="delete-outline" @click="deleteDevises" v-if="checkedRows.length">Supprimer</b-button>
            </div>
        </title-bar>

        <section class="section is-main-section">
            <b-table 
                :data="devises.data"
                striped
                hoverable

                checkable
                :checked-rows.sync="checkedRows"
            >
                <!-- :default-sort-direction="defaultSortOrder" -->
                <b-table-column field="name" label="Nom" v-slot="props">
                    {{ props.row.name }}
                </b-table-column>
                <b-table-column field="symbole" label="Symbole" v-slot="props">
                    {{ props.row.symbole }}
                </b-table-column>
                <b-table-column width="40" numeric v-slot="props">
                    <b-button @click="editDevise(props.row)" size="is-small" icon-left="pencil-outline" type="is-info is-light"></b-button>
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
            <devise-form :devise="selectDevise" @resetData="selectDevise = {}" @close="isModalActive = false"></devise-form>
        </b-modal>

        <b-notification :closable="false" class="loading-notification">
            <b-loading :is-full-page="isFullPage" v-model="isDeleting" :can-cancel="false"></b-loading>
        </b-notification>
    </app-layout>
</template>

<script>
    import AppLayout from '../../Layouts/AppLayout'
    import TitleBar from '../../Menu/TitleBar'
    import { DeviseForm } from "../../components/Devises"
    import { Inertia } from '@inertiajs/inertia'

    export default {
        props: ['devises', 'message', 'errors'],
        components: {
            AppLayout,
            TitleBar,
            DeviseForm
        },
        data() {
            return {
                selectDevise: {},
                isModalActive: false,
                loading: false,
                checkedRows: [],

                isFullPage: true,
                isDeleting: false,
            }
        },
        
        methods: {
            addDevise() {
                this.selectDevise = {}
                this.isModalActive = true
            },
            editDevise(devise) {
                this.selectDevise = devise
                this.isModalActive = true
            },
            deleteDevises() {
                if (this.checkedRows.length) {
                    this.$buefy.dialog.confirm({
                        title: 'Supprimer devise(s)',
                        message: 'Etes-vous sûrs de vouloir <b>supprimer</b> ce(s) devise(s) ?<br/> Cette action ne peut pas être annulée.',
                        confirmText: 'Supprimer devise(s)',
                        type: 'is-danger',
                        hasIcon: true,
                        size: 'is-small',
                        onConfirm: () => {
                            // this.$buefy.toast.open('Account deleted!')
                            this.isDeleting = true
                            var checkedForm = {
                                checkedRows: this.checkedRows
                            }
                            this.$inertia.post('/devises/delete-devises', checkedForm)
                            .then(() => {
                                if (this.$page.flash.message != null ) {
                                    this.checkedRows = []
                                    this.$buefy.notification.open({
                                        message: 'Devise(s) supprimé(s) avec succès.',
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
                return ['Devises']
            }
        },

        // watch: {
        //     checkedData: function () {
        //         this.deleteUsersForm.checkedData = this.checkedData
        //     }
        // },
    }
</script>
