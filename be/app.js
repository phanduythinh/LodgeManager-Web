const express = require('express')

const app = express()

const postsRouter = require('./routes/posts')
app.use("/posts", postsRouter)

module.exports = app