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
    "/orders/:id/shipper/accept",
    asyncHandler(async (req, res) => {
      // const body = {
      //   email: req.body.email,
      //   password: req.body.password,
      // };
  
      // const result = await axios
      //   .post(auth + "/login", body, options)
      //   .then((response) => {
      //     res.status(200).json(response.data);
      //     return;
      //   })
      //   .catch((err) => {
      //     res.status(err.response.status).json(err.response.data);
      //     return;
      //   });
    })
  );

  router.post(
    "/orders/:id/shipper/deny",
    asyncHandler(async (req, res) => {
      // const body = {
      //   email: req.body.email,
      //   password: req.body.password,
      // };
  
      // const result = await axios
      //   .post(auth + "/login", body, options)
      //   .then((response) => {
      //     res.status(200).json(response.data);
      //     return;
      //   })
      //   .catch((err) => {
      //     res.status(err.response.status).json(err.response.data);
      //     return;
      //   });
    })
  );

  router.post(
    "/orders/:id/shipper/complete",
    asyncHandler(async (req, res) => {
      // const body = {
      //   email: req.body.email,
      //   password: req.body.password,
      // };
  
      // const result = await axios
      //   .post(auth + "/login", body, options)
      //   .then((response) => {
      //     res.status(200).json(response.data);
      //     return;
      //   })
      //   .catch((err) => {
      //     res.status(err.response.status).json(err.response.data);
      //     return;
      //   });
    })
  );