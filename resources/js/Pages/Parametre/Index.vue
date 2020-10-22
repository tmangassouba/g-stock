<template>
    <app-layout>
        <title-bar :title-stack="['Profil de l\'organisation']"></title-bar>

        <section class="section is-main-section">
            <div class="columns">
                <div class="column">
                    <b-field
                        horizontal 
                        label="Logo"
                        :type="$page.errors.name ? 'is-danger' : ''"
                        :message="$page.errors.name ? $page.errors.name[0] : ''"
                        class="field-label is-small">
                        <div class="columns">
                            <div class="column">
                                <div v-if="organisation && organisation.image" style="border: 1px solid #d6d6d6;">
                                    <b-image
                                        :src="organisation.image_url"
                                        :alt="organisation.name"
                                        >
                                    </b-image>
                                </div>
                                <b-upload v-model="logo" drag-drop expanded :loading="changingImage" @input="changeImage" v-else>
                                    <section class="section">
                                        <div class="content has-text-centered">
                                            <p>
                                                <b-icon
                                                    icon="camera"
                                                    size="is-medium">
                                                </b-icon>
                                            </p>
                                        </div>
                                    </section>
                                </b-upload>
                            </div>

                            <div class="column">
                                <div>Ce logo apparaîtra sur les documents (devis, factures, etc.) qui sont créés.</div>
                                <div v-if="$page.errors.photo"> {{ $page.errors.photo[0] }}</div>
                                <b-button
                                    v-if="organisation && organisation.image"
                                    type="is-text"
                                    size="is-small"
                                    outlined
                                    :loading="deletingImage"
                                    @click="deleteImage"
                                    style="padding:0">
                                    Supprimer la photo
                                </b-button>
                            </div>
                        </div>
                    </b-field>

                    <form @submit.prevent="submit" v-if="form">
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
                            label="Adresse 1"
                            :type="$page.errors.adresse_1 ? 'is-danger' : ''"
                            :message="$page.errors.adresse_1 ? $page.errors.adresse_1[0] : ''"
                            class="field-label is-small">
                            <b-input name="adresse_1" v-model="form.adresse_1" size="is-small" expanded></b-input>
                        </b-field>

                        <b-field
                            horizontal 
                            label="Adresse 2"
                            :type="$page.errors.adresse_2 ? 'is-danger' : ''"
                            :message="$page.errors.adresse_2 ? $page.errors.adresse_2[0] : ''"
                            class="field-label is-small">
                            <b-input name="adresse_2" v-model="form.adresse_2" size="is-small" expanded></b-input>
                        </b-field>

                        <b-field
                            horizontal 
                            label="Ville"
                            :type="$page.errors.ville ? 'is-danger' : ''"
                            :message="$page.errors.ville ? $page.errors.ville[0] : ''"
                            class="field-label is-small">
                            <b-input name="ville" v-model="form.ville" size="is-small" expanded></b-input>
                        </b-field>

                        <b-field
                            horizontal 
                            label="Pays"
                            :type="$page.errors.pays ? 'is-danger' : ''"
                            :message="$page.errors.pays ? $page.errors.pays[0] : ''"
                            class="field-label is-small">
                            <b-input name="pays" v-model="form.pays" size="is-small" expanded></b-input>
                        </b-field>

                        <b-field
                            horizontal 
                            label="Téléphone"
                            :type="$page.errors.phone ? 'is-danger' : ''"
                            :message="$page.errors.phone ? $page.errors.phone[0] : ''"
                            class="field-label is-small">
                            <b-input name="phone" v-model="form.phone" size="is-small" expanded></b-input>
                        </b-field>

                        <b-field
                            horizontal 
                            label="Faxe"
                            :type="$page.errors.faxe ? 'is-danger' : ''"
                            :message="$page.errors.faxe ? $page.errors.faxe[0] : ''"
                            class="field-label is-small">
                            <b-input name="faxe" v-model="form.faxe" size="is-small" expanded></b-input>
                        </b-field>

                        <b-field
                            horizontal
                            
                            
                            label="Site"
                            :type="$page.errors.site ? 'is-danger' : ''"
                            :message="$page.errors.site ? $page.errors.site[0] : ''"
                            class="field-label is-small">
                            <b-input type="url" name="site" v-model="form.site" placeholder="http://www.exemple.com" size="is-small" expanded></b-input>
                        </b-field>

                        <b-field
                            horizontal 
                            label="Email"
                            :type="$page.errors.email ? 'is-danger' : ''"
                            :message="$page.errors.email ? $page.errors.email[0] : ''"
                            class="field-label is-small">
                            <b-input type="email" name="email" v-model="form.email" size="is-small" expanded></b-input>
                        </b-field>

                        <b-field
                            horizontal 
                            label="Devise"
                            :type="$page.errors.devise_id ? 'is-danger' : ''"
                            :message="$page.errors.devise_id ? $page.errors.devise_id[0] : ''"
                            class="field-label is-small">
                            <b-select placeholder="Devise" name="devise_id" v-model="form.devise_id" size="is-small" expanded>
                                <option
                                    v-for="devise in devises"
                                    :value="devise.id"
                                    :key="devise.id">
                                    {{ devise.name }} ({{ devise.symbole }})
                                </option>
                            </b-select>
                        </b-field>

                        <b-field>
                            <b-button size="is-small" type="is-info" native-type="submit" :loading="savingData">Modifier</b-button>
                        </b-field>
                    </form>
                </div>
                <div class="column is-3"></div>
            </div>
        </section>
    </app-layout>
</template>

<script>
    import { mapActions, mapGetters } from 'vuex'
    import AppLayout from '../../Layouts/AppLayout'
    import TitleBar from '../../Menu/TitleBar'
    import { Inertia } from '@inertiajs/inertia'

    export default {
        components: {
            AppLayout,
            TitleBar
        },
        data() {
            return {
                isFullPage: true,
                savingData: false,
                changingImage: false,
                deletingImage: false,
                devises: [],
                form: {},
                logo: null
            }
        },
        computed: {
            ...mapGetters({
                organisation: 'parametres/getParametre',
            }),
        },
        methods: {
            ...mapActions({
                getEntreprise: 'parametres/getEntreprise'
            }),
            submit() {
                this.savingData = true
                this.$inertia.put('/entreprise/', this.form)
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
                    this.savingData = false
                })
            },
            changeImage(file) {
                // console.log(file)
                let data = new FormData()
                data.append('photo', file || '')
                this.changingImage = true
                Inertia.post('/entreprise/change-image', data)
                .then(() => {
                    if (this.$page.flash.message != null ) {
                        this.$inertia.visit('/profil-organisation')
                    }
                }).finally(() => {
                    this.changingImage = false
                })
            },
            deleteImage() {
                this.deletingImage = true
                this.$inertia.delete('/entreprise/delete-image')
                .then(() => {
                    if (this.$page.flash.message != null ) {
                        this.$inertia.visit('/profil-organisation')
                    }
                }).finally(() => {
                    this.deletingImage = false
                })
            }
        },
        watch: {
            organisation: function() {
                this.form = {
                    'id': this.organisation.id,
                    'name': this.organisation.name,
                    'adresse_1': this.organisation.adresse_1,
                    'adresse_2': this.organisation.adresse_2,
                    'ville': this.organisation.ville,
                    'pays': this.organisation.pays,
                    'phone': this.organisation.phone,
                    'faxe': this.organisation.faxe,
                    'site': this.organisation.site,
                    'email': this.organisation.email,
                    'devise_id': this.organisation.devise_id
                }
            }
        },
        created() {
            this.getEntreprise()
            axios.get('/api/devises')
            .then((data) => {
                this.devises = data.data
            })
        },
    }
</script>
