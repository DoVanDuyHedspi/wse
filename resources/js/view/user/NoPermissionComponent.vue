<template>
  <div class="container error-container">
    <div class="row d-flex align-items-center justify-content-center">
      <div class="col-md-12 text-center">
        <h1 class="big-text">Sorry!</h1>
      </div>
      <div class="col-md-12 text-center">
        <h4>Tài khoản không có quyền truy cập vào đường dẫn này!</h4>
      </div>
      <div class="text-center mt-4">
        <router-link to=""></router-link>
        <button class="button button-dark-blue iq-mt-15" @click="$router.go(-1)">Quay lại</button>
      </div>
    </div>
  </div>
</template>
<style lang="scss" scoped>
.error-container {
  margin-top: 100px;
  display: flex;
  flex-direction: column;
  justify-content: center;
  align-items: center;
  font-family: montserrat, sans-serif;
}

.big-text {
  font-size: 150px;
  font-weight: 900;
  font-family: sans-serif;
  background: url("https://cdn.pixabay.com/photo/2018/05/30/15/39/thunderstorm-3441687_960_720.jpg")
    no-repeat;
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  background-size: cover;
  background-position: center;
}

.small-text {
  font-family: montserrat, sans-serif;
  color: rgb(0, 0, 0);
  font-size: 24px;
  font-weight: 700;
  text-transform: uppercase;
}

.button {
  color: #fff;
  padding: 12px 36px;
  font-weight: 600;
  border: none;
  position: relative;
  font-family: "Raleway", sans-serif;
  display: inline-block;
  text-transform: uppercase;
  -webkit-border-radius: 90px;
  -moz-border-radius: 90px;
  border-radius: 90px;
  margin: 2px;
  margin-top: 2px;
  background-image: linear-gradient(
    to right,
    #09b3ef 0%,
    #1e50e2 51%,
    #09b3ef 100%
  );
  background-size: 200% auto;
  // flex: 1 1 auto;
  text-decoration: none;
  margin: auto !important;
}

.button:hover,
.button:focus {
  color: #ffffff;
  background-position: right center;
  -webkit-box-shadow: 0px 5px 15px 0px rgba(0, 0, 0, 0.1);
  -moz-box-shadow: 0px 5px 15px 0px rgba(0, 0, 0, 0.1);
  box-shadow: 0px 5px 15px 0px rgba(0, 0, 0, 0.1);
  text-decoration: none;
}
</style>