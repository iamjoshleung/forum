<template>
    <li class="nav-item dropdown" v-if="notifications.length">
        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            <i class="fas fa-bell"></i>
        </a>
        <div class="dropdown-menu" aria-labelledby="navbarDropdown">
            <a class="dropdown-item" :href="notification.data.link" v-for="notification in notifications" :key="notification.id" @click="markAsRead(notification)">{{ notification.data.message }}</a>
        </div>
    </li>
</template>


<script>
export default {
  data() {
    return {
      notifications: []
    };
  },
  created() {
    axios
      .get(`/profile/${window.App.user.name}/notifications`)
      .then(res => (this.notifications = res.data));
  },
  methods: {
    markAsRead(notification) {
      axios.delete(
        `/profile/${window.App.user.name}/notifications/${notification.id}`
      );
    }
  }
};
</script>