<template>
    <app-layout>
        <title-bar :title-stack="titleStack">
            <div class="buttons is-right" v-if="gerant">
                <b-button class="is-info is-small" icon-left="plus" tag="a" href="/operations/ajouter">Nouveau</b-button>
                <b-button class="is-danger is-small" icon-left="delete-outline" @click="deleteOperations" v-if="checkedRows.length">Supprimer</b-button>
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
                :data="operations.data"
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
                <b-table-column field="reference" label="Réf." sortable v-slot="props" numeric>
                    <inertia-link :href="'/operations/' + props.row.reference">{{ props.row.reference }}</inertia-link>
                </b-table-column>
                <b-table-column field="date" label="Date" sortable v-slot="props">
                    <inertia-link :href="'/operations/' + props.row.reference">{{ props.row.date_formated }}</inertia-link>
                </b-table-column>
                <b-table-column field="type" label="Operation" sortable v-slot="props">
                    <b-tag :type="tagType(props.row.type) + ' is-light'">
                        {{ props.row.type }} :
                        {{ props.row.magazinFrom ? props.row.magazinFrom.name : '' }}
                        <b-icon icon="arrow-right" size="is-small"></b-icon>
                        {{ props.row.magazinTo ? props.row.magazinTo.name : '' }}
                    </b-tag>
                </b-table-column>
                <b-table-column field="description" label="Description" v-slot="props" centered>
                    <b-tooltip :label="props.row.description ? props.row.description : '-'" type="is-dark">
                        <b-icon icon="message-bulleted" size="is-small"></b-icon>
                    </b-tooltip>
                </b-table-column>
                <b-table-column field="user_id" label="Par" sortable v-slot="props">
                    {{ props.row.user ? props.row.user.name : '-' }}
                </b-table-column>
                <!-- <b-table-column field="created_at" label="Créé le" numeric sortable v-slot="props">
                    {{ props.row.created_at }}
                </b-table-column> -->

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
        props: ['operations', 'sortField', 'sortOrder', 'message', 'errors', 'gerant'],
        components: {
            AppLayout,
            TitleBar,
        },
        data() {
            return {
                // selectArticle: {},
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
                Inertia.visit('/operations', {
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
            deleteOperations() {
                if (this.checkedRows.length) {
                    this.$buefy.dialog.confirm({
                        title: 'Supprimer opérations',
                        message: 'Etes-vous sûrs de vouloir <b>supprimer</b> ce(s) éléments(s) ?<br/> Cette action ne peut pas être annulée.',
                        confirmText: 'Supprimer opération(s)',
                        type: 'is-danger',
                        hasIcon: true,
                        size: 'is-small',
                        onConfirm: () => {
                            // this.$buefy.toast.open('Account deleted!')
                            this.isDeleting = true
                            var checkedForm = {
                                checkedRows: this.checkedRows
                            }
                            this.$inertia.post('/operations/delete', checkedForm)
                            .then(() => {
                                if (this.$page.flash.message != null ) {
                                    // this.resetForm()
                                    this.$buefy.notification.open({
                                        message: 'Opération(s) supprimée(s) avec succès.',
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
            },
            tagType(type) {
                if (type == this.$OPERATION_SORTIE) {
                    return 'is-danger'
                }
                if (type == this.$OPERATION_TRANSFERT) {
                    return 'is-info'
                }
                return 'is-success'
            }
        },
        created() {
            if (this.operations) {
                this.currentPage = this.operations.current_page
                this._sortField = this.sortField
                this._sortOrder = this.sortOrder
                this.perPage = this.operations.per_page
                this.total = this.operations.total
            }
        },

        computed: {
            titleStack () {
                return ['Operations']
            }
        },
    }
</script>
