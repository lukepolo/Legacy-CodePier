<template>
    <section id="middle" class="section-column">
        <h3 class="section-header primary">My Piles</h3>

        <div class="section-content">
            <div class="container">
                <h1>Welcome. Let's Get Started.</h1>
                <p class="info">Piles are groupings for your servers. We've built defaults for you, but you can edit them to fit your needs.</p>

                <div class="group-container">

                    <template v-for="pile in user.piles">
                        <div class="group">
                            <div class="group-heading">
                                <h4>{{ pile.name }}</h4>
                            </div>

                            <template v-if="pile.servers.length">
                                <div class="group-content">
                                    <h4>Servers</h4>

                                    <div class="server-list">
                                        <a class="server">
                                            <div class="server-name">CodePier 9.0</div>
                                        </a>
                                        <a class="server">
                                            <div class="server-name">Switchblade.io</div>
                                        </a>
                                    </div>
                                </div>
                            </template>


                            <div class="btn-footer text-center">
                                <button class="btn">Edit</button>
                                <button class="btn">Delete</button>
                                <a class="btn btn-primary" href="create-server.html">Create Server</a>
                            </div>
                        </div>
                    </template>

                    <div class="group">
                        <a class="add-pile">
                            <div class="group-content">
                                <span class="icon-layers"></span>
                            </div>

                            <div class="btn-footer text-center">
                                <button class="btn btn-primary">Add Pile</button>
                            </div>
                        </a>
                    </div>

                    <div class="group">
                        <div class="group-heading">
                            <h4><input type="text" placeholder="Pile Name"></h4>
                        </div>

                        <div class="group-content">

                            <div class="jcf-form-wrap">

                            </div>
                        </div>

                        <div class="btn-footer text-center">
                            <button class="btn">Cancel</button>
                            <button class="btn btn-primary">Save</button>
                        </div>
                    </div>

                </div><!-- end group-container -->
            </div>
        </div>
    </section>
</template>

<script>
    export default {
        data() {
            return {
                user : user
            }
        }
    }
</script>