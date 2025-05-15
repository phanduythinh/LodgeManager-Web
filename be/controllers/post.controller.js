function index(req, res) {
  const posts = "Post list"
  res.send(posts)
}

module.exports={
  index: index
}