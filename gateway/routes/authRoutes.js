const express = require("express");
const axios = require("axios");
const router = express.Router();

const asyncHandler = require("express-async-handler");

const auth = process.env.AUTH;
const shop = process.env.SHOP;
const options = {
  headers: { Accept: "application/json" },
};

router.post(
  "/login",
  asyncHandler(async (req, res) => {
    const body = {
      email: req.body.email,
      password: req.body.password,
    };

    const result = await axios
      .post(auth + "/login", body, options)
      .then((response) => {
        res.status(200).json(response.data);
        return;
      })
      .catch((err) => {
        res.status(err.response.status).json(err.response.data);
        return;
      });
  })
);

router.post(
  "/register",
  asyncHandler(async (req, res) => {
    const account = {
      name: req.body.name,
      email: req.body.email,
      password: req.body.password,
      password_confirmation: req.body.password_confirmation,
      role: req.body.role,
    };
    let store = {
      TEN_CUA_HANG: req.body.TEN_CUA_HANG,
      SDT: req.body.SDT,
      EMAIL: req.body.EMAIL,
      DIA_CHI: req.body.DIA_CHI,
      LOGO: req.body.LOGO,
      // id: req.body.id,
    };
    if (
      store["TEN_CUA_HANG"] === undefined ||
      store["TEN_CUA_HANG"] == null ||
      store["SDT"] === undefined ||
      store["SDT"] == null ||
      store["DIA_CHI"] === undefined ||
      store["DIA_CHI"] == null
    )
      return res.status(401).json({ error: "Thiếu thông tin cửa hàng" });

    // console.log(req.get('Authorization'));

    let newAccount = null;
    let newStore = null;
    let error = null;
    await axios
      .post(auth + "/register", account, options)
      .then((response) => {
        // res.status(200).json(response.data);
        newAccount = response.data;
      })
      .catch((err) => {
        console.log(err.response);
        error = err.response.data;
      });

    if (error != null) res.status(200).json(error);
    if (newAccount['user']["role"] !== "SELLER") return res.status(200).json({ account });
    
    let token = newAccount["token"].split("|")[1];
    //console.log(newAccount["token"]);
    //console.log(newAccount['id']);
    //return res.status(200).json(newAccount);

    let op = { ...options };
    
    op["headers"]["Authorization"] = "Bearer " + token;
    store["id"] = newAccount["user"]["id"];
    await axios
      .post(auth + "/store", store, op)
      .then((response) => {
        // res.status(200).json(response.data);
        newStore = response.data;
      })
      .catch((err) => {
        console.log(err.response);
        error = err.response.data;
      });
    if (error != null) {
      await axios
        .delete(auth + "/dropuser/" + newAccount["user"]["id"], null, options)
        .then((response) => {})
        .catch((err) => {
          console.log(err.response);
          error = [error, err.response.data];
        });
      return res.status(200).json(error);
    }

    return res.status(200).json({ account, store });
  })
);

module.exports = router;
