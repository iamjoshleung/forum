<template>
    <button :class="classes" @click="toggle">
        <i class="fas fa-heart"></i>
        <span>{{ count }}</span>
    </button>
</template>

<script>
export default {
  props: ["reply"],
  data() {
    return {
      count: this.reply.favouritesCount,
      active: this.reply.isFavourited
    };
  },
  computed: {
    classes() {
      return ["btn", this.active ? "btn-primary" : "btn-default"];
    }
  },
  methods: {
    toggle() {
      return this.active ? this.destroy() : this.create();
    },
    create() {
      axios.post(`/replies/${this.reply.id}/favourites`);
      this.active = true;
      this.count++;
    },
    destroy() {
      axios.delete(`/replies/${this.reply.id}/favourites`);
      this.active = false;
      this.count--;
    }
  }
};
</script>