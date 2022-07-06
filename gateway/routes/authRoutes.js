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
    const body = {
      name: req.body.name,
      email: req.body.email,
      password: req.body.password,
      password_confirmation: req.body.password_confirmation,
      role: req.body.role,
      TEN: req.body.TEN,
      CMND: req.body.CMND,
      SDT: req.body.SDT,
      NGAY_SINH: req.body.NGAY_SINH,
      DIA_CHI: req.body.DIA_CHI,
    };
    const result = await axios
      .post(auth + "/register", body, options)
      .then((response) => {
        res.status(200).json(response.data);
        return;
      })
      .catch((err) => {
        console.log(err.response);
        res.status(err.response.status).json(err.response.data);
        return;
      });
  })
);

module.exports = router;
