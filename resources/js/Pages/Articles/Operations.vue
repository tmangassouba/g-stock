<template>
    <app-layout>
        <title-bar :title-stack="titleStack">
            <div class="buttons is-right" v-if="product && product.code">
                <b-button class="is-info is-small" icon-left="arrow-left" tag="a" :href="'/articles/' + product.code">Retour</b-button>
            </div>
        </title-bar>

        <section class="section is-main-section">
            <b-table 
                :data="operations.data"
                :loading="loading"
                striped
                hoverable

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
                <b-table-column field="reference" label="Réf." width="100" v-slot="props" numeric>
                    <inertia-link :href="'/operations/' + props.row.reference">{{ props.row.reference }}</inertia-link>
                </b-table-column>
                <b-table-column field="date" label="Date" v-slot="props">
                    <inertia-link :href="'/operations/' + props.row.reference">{{ props.row.date_formated }}</inertia-link>
                </b-table-column>
                <b-table-column field="type" label="Operation" sortable v-slot="props">
                        {{ props.row.type }} :
                        {{ props.row.magazinFrom ? props.row.magazinFrom.name : '' }}
                        <b-icon icon="arrow-right" size="is-small"></b-icon>
                        {{ props.row.magazinTo ? props.row.magazinTo.name : '' }}
                        <b-tag v-if="props.row.validated == 0">Brouillon</b-tag>
                </b-table-column>
                <b-table-column field="user_id" label="Par" v-slot="props">
                    {{ props.row.user ? props.row.user.name : '-' }}
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
    </app-layout>
</template>

<script>
    import AppLayout from '../../Layouts/AppLayout'
    import TitleBar from '../../Menu/TitleBar'
    import { Inertia } from '@inertiajs/inertia'

    export default {
        props: ['operations', 'sortField', 'sortOrder', 'message', 'errors', 'product'],
        components: {
            AppLayout,
            TitleBar,
        },
        data() {
            return {
                // selectArticle: {},
                isModalActive: false,
                loading: false,
                total: null,
                perPage: null,
                currentPage: null,
                _sortField: null,
                _sortOrder: null,
                defaultSortOrder: 'asc',
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
            console.log(this.operations)
        },

        computed: {
            titleStack () {
                let title = this.product ? this.product.designation + ' > Operations' : 'Operations'
                return [title]
            }
        },
    }
</script>
