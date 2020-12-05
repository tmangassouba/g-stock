<template>
    <app-layout>
        <title-bar :title-stack="titleStack">
            <div class="buttons is-right" v-if="admin">
                <b-button class="is-info is-small" icon-left="plus" tag="a" href="/clients/ajouter">Nouveau</b-button>
                <b-button class="is-danger is-small" icon-left="delete-outline" @click="deleteData" v-if="checkedRows.length">Supprimer</b-button>
            </div>
        </title-bar>

        <section class="section is-main-section">
            <b-table 
                :data="customers.data"
                :loading="loading"
                striped
                hoverable

                checkable
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
                <!-- :default-sort-direction="defaultSortOrder" -->
                <!-- <b-table-column field="code" label="Ref." sortable v-slot="props">
                    <inertia-link :href="'/clients/' + props.row.code">{{ props.row.code }}</inertia-link>
                </b-table-column> -->
                <b-table-column field="name" label="Client" sortable v-slot="props">
                    <inertia-link :href="'/clients/' + props.row.code">
                        {{ props.row.title }}
                        {{ props.row.name }}
                    </inertia-link>
                </b-table-column>
                <b-table-column field="company" label="Compagnie" sortable v-slot="props">
                    {{ props.row.company ? props.row.company : '-' }}
                </b-table-column>
                <b-table-column field="city" label="Adresse" sortable v-slot="props">
                    {{ props.row.address}}{{ props.row.address && props.row.city ? ', ' : '' }}
                    {{ props.row.city }}
                </b-table-column>
                <b-table-column field="telephone" label="Téléphone" sortable v-slot="props">
                    <a :href="'tel:' + props.row.telephone" v-if="props.row.telephone">{{ props.row.telephone }}</a>
                    <span v-else>-</span>
                </b-table-column>
                <!-- <b-table-column field="mobile" label="Mobile" sortable v-slot="props">
                    <a :href="'tel:' + props.row.mobile" v-if="props.row.mobile">{{ props.row.mobile }}</a>
                    <span v-else>-</span>
                </b-table-column> -->
                <b-table-column field="email" label="Email" sortable v-slot="props">
                    <a :href="'mailto:' + props.row.email" v-if="props.row.email">{{ props.row.email }}</a>
                    <span v-else>-</span>
                </b-table-column>
                <b-table-column field="website" label="Site web" sortable v-slot="props">
                    <a :href="props.row.website" target="_blank" v-if="props.row.website">{{ props.row.website }}</a>
                    <span v-else>-</span>
                </b-table-column>

                <template slot="empty">
                    <section class="section">
                        <div class="content has-text-grey has-text-centered">
                            <div><b-icon icon="inbox" size="is-large"></b-icon></div>
                            <p>Aucun résultat.</p>
                        </div>
                    </section>
                </template>
            </b-table>
        </section>

        <b-notification :closable="false" class="loading-notification">
            <b-loading :is-full-page="isFullPage" v-model="isDeleting" :can-cancel="false"></b-loading>
        </b-notification>
    </app-layout>
</template>

<script>
    import AppLayout from '../../Layouts/AppLayout'
    import TitleBar from '../../Menu/TitleBar'
    import { Inertia } from '@inertiajs/inertia'

    export default {
        props: ['customers', 'sortField', 'sortOrder', 'message', 'errors', 'admin'],
        components: {
            AppLayout,
            TitleBar,
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
                Inertia.visit('/clients', {
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
            deleteData() {
                if (this.checkedRows.length) {
                    this.$buefy.dialog.confirm({
                        title: 'Supprimer clients',
                        message: 'Etes-vous sûrs de vouloir <b>supprimer</b> ce(s) client(s) ?<br/> Cette action ne peut pas être annulée.',
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
                                    this.checkedFilesRows = []
                                    this.$buefy.notification.open({
                                        message: 'Client(s) supprimé(s) avec succès.',
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
            if (this.customers) {
                this.currentPage = this.customers.meta.current_page
                this._sortField = this.sortField
                this._sortOrder = this.sortOrder
                this.perPage = this.customers.meta.per_page
                this.total = this.customers.meta.total
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
                return ['Clients']
            }
        },

        // watch: {
        //     checkedData: function () {
        //         this.deleteUsersForm.checkedData = this.checkedData
        //     }
        // },
    }
</script>
