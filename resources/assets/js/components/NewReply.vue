<template>

  <div v-if="signedIn">
    <div class="form-group">
      <wysiwyg name="body" placeholder="Have something to say?" v-model="body" :shouldClear="completed"></wysiwyg>
    </div>

    <button type="submit" class="btn btn-default" @click="submit">Post</button>
  </div>
  <p class="text-center" v-else>Please
    <a href="/login">sign in</a> to participate in this discussion.
  </p>
</template>

<script>
import "jquery.caret";
import "at.js";

export default {
  props: ["endpoint"],
  data() {
    return {
      body: "",
      completed: false
    };
  },
  mounted() {

    $("#body").atwho({
      at: "@",
      limit: 5,
      delay: 300,
      callbacks: {
        remoteFilter: function(query, callback) {
          // console.log('called');
          $.getJSON("/api/users", { q: query }, function(data) {
            callback(data);
          });
        }
      }
    });
  },
  methods: {
    submit() {
      axios
        .post(`${location.pathname}/replies`, { body: this.body })
        .then(({ data }) => {
          this.body = "";
          this.completed = true;
          flash("You reply has been added");
          this.$emit("created", data);
        })
        .catch(err => {
          flash(err.response.data, "danger");
        });
    }
  }
};
</script>