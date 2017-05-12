<template>
    <section>
        <left-nav></left-nav>
        <section id="middle" class="section-column">
            <h3 class="section-header primary">My Piles</h3>
            <div class="section-content">
                <div class="container">
                    <h1>Welcome. Let's Get Started.</h1>
                    <p class="info">Piles are groupings for your sites. We've built defaults for you, but you can edit them to fit your needs.</p>
                    <div class="group">
                        <pile v-on:deletePile="deletePile(index)" :pile="pile" :index="index" :key="pile.id" v-for="(pile, index) in piles"></pile>
                        <div class="group--item">
                            <a @click="newPile()" class="add-pile">
                                <div class="group--item-content">
                                    <span class="icon-layers"></span>
                                </div>

                                <div class="btn-footer text-center">
                                    <button class="btn btn-primary">Add Pile</button>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </section>
</template>

<script>
    import Pile from './components/Pile.vue';
    import LeftNav from '../../core/LeftNav.vue';

    export default {
        components: {
            Pile,
            LeftNav
        },
        computed: {
            piles() {
                return this.$store.state.pilesStore.piles;
            }
        },
        methods: {
            deletePile(index) {
                this.$store.commit('REMOVE_TEMP_PILE', index)
            },
            newPile() {
                this.$store.state.pilesStore.piles.push({
                    name: null,
                    editing: true
                });
            }
        }
    }
</script>