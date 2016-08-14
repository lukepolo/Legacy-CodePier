<template>
    <section>
        <left-nav></left-nav>
        <section id="middle" class="section-column">
            <server-nav :server_id="this.$route.params.server_id"></server-nav>
            <form>
                <div class="form-group">
                    {!! Form::label('name') !!}
                    {!! Form::text('name', null, ['class' => 'form-control']) !!}
                </div>
                <div class="form-group">
                    <label>Public Key</label>
                    <textarea name="ssh_key"></textarea>
                </div>
                <button type="submit">Install SSH KEY</button>
            </form>
            <table class="table">
                <thead>
                <tr>
                    <th>Key Name</th>
                    <th></th>
                </tr>
                </thead>
                <tbody>
                <tr>
                    <td>key name</td>
                    <td><a href="#" class="fa fa-remove"></a></td>
                </tr>
                </tbody>
            </table>
        </section>
    </section>
</template>

<script>
    import ServerNav from './components/ServerNav.vue';
    import LeftNav from './../../core/LeftNav.vue';

    export default {
        components : {
            LeftNav,
            ServerNav
        },
        data() {
            return {
                server : null
            }
        },
        computed : {

        },
        methods : {

        },
        mounted() {

            console.log(this.$route.params);
            Vue.http.get(this.action('Server\ServerController@show', {server : this.$route.params.server_id})).then((response) => {
                this.server = response.json();
            }, (errors) => {
                alert(error);
            });
        }
    }
</script>
