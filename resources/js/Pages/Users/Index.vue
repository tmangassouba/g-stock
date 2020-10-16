<template>
    <app-layout>
        <title-bar :title-stack="titleStack">
            <div class="buttons is-right" v-if="admin">
                <b-button class="is-info is-small" icon-left="plus" @click="isModalActive = true">Nouveau</b-button>
                <b-button class="is-danger is-small" icon-left="delete-outline" @click="deleteUsers" v-if="checkedRows.length">Supprimer</b-button>
            </div>
        </title-bar>

        <section class="section is-main-section">
            <!-- <b-notification
                v-if="message"
                type="is-success"
                has-icon
                aria-close-label="Close notification">
                {{ message }}
            </b-notification> -->

            <b-table 
                :data="users.data"
                :loading="loading"
                striped
                hoverable

                checkable
                :is-row-checkable="(row) => row.id !== authUserId"
                :checked-rows.sync="checkedRows"

                paginated
                pagination-simple
                backend-pagination
                :total="total"
                :per-page="perPage"
                :current-page="currentPage"
                @page-change="onPageChange"
                pagination-size="is-small"
                aria-next-label="Suivant"
                aria-previous-label="Précédent"
                aria-page-label="Page"
                aria-current-label="Page actuelle"

                backend-sorting
                :default-sort="[_sortField, _sortOrder]"
                @sort="onSort"
            >
                <b-table-column label="#" width="40" numeric v-slot="props">
                    {{ props.index + 1 }}
                </b-table-column>
                <b-table-column field="first_name" label="Prénom." sortable v-slot="props">
                    {{ props.row.first_name }}
                </b-table-column>
                <b-table-column field="last_name" label="Nom" sortable v-slot="props">
                    {{ props.row.last_name }}
                </b-table-column>
                <b-table-column field="email" label="Email" sortable v-slot="props">
                    {{ props.row.email }}
                </b-table-column>
                <b-table-column field="phone" label="Téléphone" sortable v-slot="props">
                    {{ props.row.phone ? props.row.phone : '-' }}
                </b-table-column>
                <b-table-column field="roles" label="Rôles" v-slot="props">
                    <b-taglist>
                        <b-tag type="is-link is-light" v-for=" role in props.row.roles" :key="role.id">{{ role.name }}</b-tag>
                    </b-taglist>
                </b-table-column>
                <b-table-column field="actif" label="État" sortable v-slot="props">
                    <b-tag type="is-success is-light" v-if="props.row.actif">Actif</b-tag>
                    <b-tag type="is-danger is-light" v-else>Inactif</b-tag>
                </b-table-column>
                <b-table-column field="created_at" label="Ajouté le" sortable v-slot="props">
                    {{ props.row.created_at }}
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
            <user-form @close="isModalActive = false"></user-form>
        </b-modal>

        <b-notification :closable="false">
            <b-loading :is-full-page="isFullPage" v-model="isDeleting" :can-cancel="false"></b-loading>
        </b-notification>
    </app-layout>
</template>

<script>
    import AppLayout from '../../Layouts/AppLayout'
    import TitleBar from '../../Menu/TitleBar'
    import { UserForm } from "../../components/Users"
    import { Inertia } from '@inertiajs/inertia'

    export default {
        props: ['users', 'authUserId', 'sortField', 'sortOrder', 'message', 'errors', 'admin'],
        components: {
            AppLayout,
            TitleBar,
            UserForm
        },
        data() {
            return {
                isModalActive: false,
                loading: false,
                checkedRows: [],
                total: null,
                perPage: null,
                currentPage: null,
                _sortField: null,
                _sortOrder: null,
                defaultSortOrder: 'asc',

                isFullPage: true,
                isDeleting: false,
            }
        },
        
        methods: {
            loadAsyncData() {
                Inertia.visit('/users', {
                    method: 'get',
                    data: {
                        page: this.currentPage,
                        sortField: this._sortField,
                        sortOrder: this._sortOrder,
                    }
                })
            },
            onPageChange(page) {
                this.currentPage = page
                this.loadAsyncData()
            },
            onSort(field, order) {
                this._sortField = field
                this._sortOrder = order
                this.loadAsyncData()
            },
            deleteUsers() {
                if (this.checkedRows.length) {
                    this.$buefy.dialog.confirm({
                        title: 'Supprimer utilisateur',
                        message: 'Etes-vous sûrs de vouloir <b>supprimer</b> ce(t)(s) utilisateur(s) ?<br/> Cette action ne peut pas être annulée.',
                        confirmText: 'Supprimer produit(s)',
                        type: 'is-danger',
                        hasIcon: true,
                        size: 'is-small',
                        onConfirm: () => {
                            // this.$buefy.toast.open('Account deleted!')
                            this.isDeleting = true
                            var checkedForm = {
                                checkedRows: this.checkedRows
                            }
                            this.$inertia.post('/users/delete-users', checkedForm)
                            .then(() => {
                                if (this.$page.flash.message != null ) {
                                    this.resetForm()
                                    this.$buefy.notification.open({
                                        message: 'Article(s) supprimé(s) avec succès.',
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
            if (this.users) {
                this.currentPage = this.users.current_page
                this._sortField = this.sortField
                this._sortOrder = this.sortOrder
                this.perPage = this.users.per_page
                this.total = this.users.total
            }
        },

        computed: {
            // _sortField: function () {
            //     return this.sortField
            // },
            // _sortOrder: function () {
            //     return this.sortOrder
            // },
            titleStack () {
                return ['Utilisateurs']
            }
        },

        // watch: {
        //     checkedData: function () {
        //         this.deleteUsersForm.checkedData = this.checkedData
        //     }
        // },
    }
</script>
