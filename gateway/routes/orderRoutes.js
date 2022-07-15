const express = require("express");
const axios = require("axios");
const router = express.Router();

const asyncHandler = require("express-async-handler");

const auth = process.env.AUTH;
const shop = process.env.SHOP;

const options = {
  headers: { Accept: "application/json" },
};

router.put(
    "/:id/shipper/accept",
    asyncHandler(async (req, res) => {
      let op = { ...options };
      if (op["headers"]["Authorization"] !== undefined)
        op["headers"]["Authorization"] = req.headers["authorization"];
  
      let body = {
        TRANG_THAI: 'giao hàng',
      };
  
      let result = null;
      await axios
        .put(shop + "/orders/" + req.params.id, body, op)
        .then((response) => {
          // res.status(200).json(response.data);
          result = response.data;
        })
        .catch((err) => {
          console.log(err.response);
          res.status(err.response.status).json(err.response.data);
          return;
        });
  
      // console.log(req.headers["authorization"]);
      return res.json(result);
    })
  );

  router.put(
    "/:id/shipper/deny",
    asyncHandler(async (req, res) => {
      let op = { ...options };
      if (op["headers"]["Authorization"] !== undefined)
        op["headers"]["Authorization"] = req.headers["authorization"];
  
      let body = {
        TRANG_THAI: 'chờ giao hàng',
      };
  
      let result = null;
      await axios
        .put(shop + "/orders/" + req.params.id, body, op)
        .then((response) => {
          // res.status(200).json(response.data);
          result = response.data;
        })
        .catch((err) => {
          console.log(err.response);
          res.status(err.response.status).json(err.response.data);
          return;
        });
  
      // console.log(req.headers["authorization"]);
      return res.json(result);
    })
  );

  router.put(
    "/:id/shipper/complete",
    asyncHandler(async (req, res) => {
      let op = { ...options };
      if (op["headers"]["Authorization"] !== undefined)
        op["headers"]["Authorization"] = req.headers["authorization"];
  
      let body = {
        TRANG_THAI: 'đã giao',
      };
  
      let result = null;
      await axios
        .put(shop + "/orders/" + req.params.id, body, op)
        .then((response) => {
          // res.status(200).json(response.data);
          result = response.data;
        })
        .catch((err) => {
          console.log(err.response);
          res.status(err.response.status).json(err.response.data);
          return;
        });
  
      // console.log(req.headers["authorization"]);
      return res.json(result);
    })
  );

  router.put(
    "/:id/seller/complete",
    asyncHandler(async (req, res) => {
      let op = { ...options };
      if (op["headers"]["Authorization"] !== undefined)
        op["headers"]["Authorization"] = req.headers["authorization"];
  
      let body = {
        TRANG_THAI: 'chờ giao hàng',
      };
  
      let result = null;
      await axios
        .put(shop + "/orders/" + req.params.id, body, op)
        .then((response) => {
          // res.status(200).json(response.data);
          result = response.data;
        })
        .catch((err) => {
          console.log(err.response);
          res.status(err.response.status).json(err.response.data);
          return;
        });
  
      // console.log(req.headers["authorization"]);
      return res.json(result);
    })
  );

  router.put(
    "/:id/seller/prepare",
    asyncHandler(async (req, res) => {
      let op = { ...options };
      if (op["headers"]["Authorization"] !== undefined)
        op["headers"]["Authorization"] = req.headers["authorization"];
  
      let body = {
        TRANG_THAI: 'chuẩn bị',
      };
  
      let result = null;
      await axios
        .put(shop + "/orders/" + req.params.id, body, op)
        .then((response) => {
          // res.status(200).json(response.data);
          result = response.data;
        })
        .catch((err) => {
          console.log(err.response);
          res.status(err.response.status).json(err.response.data);
          return;
        });
  
      // console.log(req.headers["authorization"]);
      return res.json(result);
    })
  );