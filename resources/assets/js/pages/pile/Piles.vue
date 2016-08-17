<template>
    <section id="middle" class="section-column">
        <h3 class="section-header primary">My Piles</h3>

        <div class="section-content">
            <div class="container">
                <h1>Welcome. Let's Get Started.</h1>
                <p class="info">Piles are groupings for your servers. We've built defaults for you, but you can edit them to fit your needs.</p>

                <div class="group-container">

                    <pile :pile="pile" :index="index" v-for="(pile, index) in piles"></pile>

                    <div class="group">
                        <a v-on:click="newPile()" class="add-pile">
                            <div class="group-content">
                                <span class="icon-layers"></span>
                            </div>

                            <div class="btn-footer text-center">
                                <button class="btn btn-primary">Add Pile</button>
                            </div>
                        </a>
                    </div>
                </div><!-- end group-container -->
            </div>
        </div>
    </section>
</template>

<script>
    import Pile from './components/Pile.vue';

    export default {
        components : {
            Pile,
        },
        computed : {
            piles: () => {
                return pileStore.state.piles;
            }
        },
        methods : {
            newPile : function() {
                pileStore.state.piles.push({
                    name : 'New Pile',
                    editing : true
                });
            }
        }
    }
</script>