<template>
  <div>
    <h3>{{ file.file_path }}</h3>
    <codemirror
      ref="content"
      :options="config"
      :value="file.contents"
      @input="updateContent"
    ></codemirror>

    <button class="btn btn-primary btn-small" @click="$emit('update', file)">
      Update File
    </button>
  </div>
</template>

<script>
import "codemirror/lib/codemirror.css";
import { codemirror } from "vue-codemirror";
import "codemirror/theme/base16-light.css";
import "codemirror/mode/javascript/javascript.js";

export default {
  components: {
    codemirror,
  },
  props: {
    file: {
      required: true,
    },
  },
  data() {
    return {
      config: {
        tabSize: 4,
        mode: "text/javascript",
        theme: "base16-light",
        lineNumbers: true,
        line: true,
      },
    };
  },
  methods: {
    updateContent(contents) {
      this.file.contents = contents;
    },
  },
};
</script>
