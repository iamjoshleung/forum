<template>
  <div :id="'reply-' + id" class="card card-default mb-2" :class="{ 'border-success': isBest }">
    <div class="card-header">
      <div class="d-flex justify-content-between">
        <div>
          <a :href="'/profile/' + reply.owner.name" v-text="reply.owner.name"></a>
          <span>said
            <span v-text="ago"></span>
          </span>
        </div>

        <div v-if="signedIn">
          <favourite :reply="reply"></favourite>
        </div>

      </div>
    </div>

    <div class="card-body">
      <div v-if="editing">
        <form @submit="update">
          <div class="form-group">
            <wysiwyg v-model="body"></wysiwyg>
            <!-- <textarea class="form-control" v-model="body" required></textarea> -->
          </div>
          <button class="btn btn-sm btn-primary">Update</button>
          <button type="button" class="btn btn-sm btn-link" @click="editing = false">Cancel</button>
        </form>

      </div>

      <div v-else v-html="body"></div>
    </div>

    <!-- @can('destroy', $reply) -->
    <div 
      class="card-footer text-muted d-flex justify-content-between" 
      v-if="authorize('owns', reply) || authorize('owns', reply.thread)">
      <div v-if="authorize('owns', reply)">
        <button class="btn btn-sm btn-secondary" @click="editing = true">Edit</button>
        <button class="btn btn-sm btn-danger" @click="destroy">Delete</button>
      </div>
      <button class="btn btn-sm btn-default" @click="markBestReply()" v-if="authorize('owns', reply.thread)">Best Reply?</button>
    </div>
    <!-- @endcan -->
  </div>
</template>


<script>
import Favourite from "./Favourite.vue";
import moment from "moment";

export default {
  props: ["reply"],
  components: {
    Favourite
  },
  data() {
    return {
      id: this.reply.id,
      editing: false,
      body: this.reply.body,
      isBest: this.reply.isBest,
    };
  },
  computed: {
    ago() {
      return moment(this.reply.created_at).fromNow();
    }
  },
  created() {
      window.event.$on("best-reply-selected", id => {
        this.isBest = id === this.id;
      });
    },
  methods: {
    update() {
      console.log("updating");
      axios
        .patch(`/replies/${this.reply.id}`, {
          body: this.body
        })
        .then(res => {
          this.editing = false;
          flash("Updated");
        })
        .catch(err => {
          flash(err.response.data, "danger");
        });
    },
    destroy() {
      axios
        .delete(`/replies/${this.reply.id}`)
        .then(res => {
          this.$emit("deleted", this.reply.id);
        })
        .catch(err => {
          console.log(err);
        });
    },
    markBestReply() {
      this.isBest = true;

      axios.post(`/replies/${this.reply.id}/best`);

      window.event.$emit("best-reply-selected", this.id);
    }
  }
};
</script>