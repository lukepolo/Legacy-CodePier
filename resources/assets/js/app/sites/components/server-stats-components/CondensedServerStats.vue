<template>
    <div class="server-info-collapsed">
        <div class="grid-3">
            <disk-usage-condensed :server="server"></disk-usage-condensed>
            <memory-allocation-condensed :server="server"></memory-allocation-condensed>
            <cpu-load-condensed :server="server"></cpu-load-condensed>
        </div>
    </div>
</template>

<script>
    import CpuLoadCondensed from './cpu-load/CpuLoadCondensed'
    import DiskUsageCondensed from './disk-usage/DiskUsageCondensed'
    import MemoryAllocationCondensed from './memory-allocation/MemoryAllocationCondensed'

    export default {
      props : {
        server : {
          required : true,
        }
      },
      components: {
        CpuLoadCondensed,
        MemoryAllocationCondensed,
        DiskUsageCondensed
      },
    }
</script>