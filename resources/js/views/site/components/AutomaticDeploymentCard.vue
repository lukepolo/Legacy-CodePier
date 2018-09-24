<template>
    <div v-if="site.user_repository_provider_id && workFlowCompleted === true">
        // TODO - API should return boolean
        // TODO - workFlowCompleted from API
        <template v-if="!site.automatic_deployment_id">
            <div class="grid--item" @click="createDeployHook">
                <div class="providers--item">
                    <div class="providers--item-header">
                        <div class="providers--item-icon">
                            <span class="icon-cloud-auto-deploy"></span>
                        </div>
                    </div>
                    <div class="providers--item-footer">
                        <div class="providers--item-footer-connect">
                            <h4>Enable Auto Deploy</h4>
                        </div>
                    </div>
                </div>
            </div>
        </template>

        <template v-else>
            <div class="grid--item" @click="removeDeployHook">
                <div class="providers--item">
                    <div class="providers--item-header">
                        <div class="providers--item-icon">
                            <span class="icon-cloud-auto-deploy active"></span>
                        </div>
                        <div class="providers--item-name">
                            <span class="text-success">Active</span>
                        </div>
                    </div>
                    <div class="providers--item-footer">
                        <div class="providers--item-footer-connect">
                            <h4>Disable Auto Deploy</h4>
                        </div>
                    </div>
                </div>
            </div>
        </template>
    </div>
</template>

<script>
export default {
  props: ["site"],
};
</script>
