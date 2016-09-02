<template>
    <section>
        <h4>{{ area }}</h4>
        <template v-for="feature in features">
            <div class="input-group input-checkbox">
                <label>
                    <input :name="'[' + area + ']'+feature.name" type="checkbox" :checked="feature.required">
                    <span class="icon"></span>{{ feature.name }}
                </label>
            </div>
            <template v-if="feature.parameters" v-for="parameter in feature.parameters">
                <div class="input-group">
                    <input :id="parameter" :name="'[' + area + ']'+ '[' + feature.name + ']'+ parameter" type="text">
                    <label :for="parameter"><span class="float-label">{{ parameter }}</span></label>
                </div>
            </template>
        </template>
        <template v-if="frameworks">
            <h2>Frameworks for {{ area }}</h2>
            <feature-area :area="language" :features="features" v-for="(features, language) in availableServerFrameworks[area]"></feature-area>
        </template>

    </section>
</template>

<script>
    export default {
        name : 'featureArea',
        props : ['area', 'features', 'frameworks'],
        computed : {
            availableServerFrameworks: () => {
                return serverStore.state.available_server_frameworks;
            }
        }
    }
</script>
