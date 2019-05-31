<?php


class Posts extends Controller
{
  public function __construct()
  {
    if (!isLoggedIn()) {
      redirect('/users/login');
    }

    $this->postModel = $this->model('Post');
    $this->userModel = $this->model('User');
  }

  public function index()
  {
    // Get posts
    $posts = $this->postModel->getPosts();
    $data = [
      'title' => 'Posts',
      'posts' => $posts

    ];
    $this->view('posts/index', $data);
  }

  public function add()
  {
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
      // Sanitize POST array
      $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
      $data = [
        'title' => 'Add Posts',
        'postTitle' => trim($_POST['title']),
        'body' => trim($_POST['body']),
        'user_id' => $_SESSION['user_id'],
        'title_err' => '',
        'body_err' => '',
      ];

      // Validate title
      if (empty($data['postTitle'])) {
        $data['title_err'] = 'Please enter title';
      }

      // Validate body
      if (empty($data['body'])) {
        $data['body_err'] = 'Please enter body text';
      }

      // Make sure no errors
      if (empty($data['title_err']) && empty($data['body_err'])) {
        //die('Success');
        // Validated
        if ($this->postModel->addPost($data)) {
          flash('post_message', 'Post Added');
          redirect('posts');
        } else {
          die('Something went wrong');
        }
      } else {
        // load view with errors
        $this->view('posts/add', $data);
      }

    } else {
      $data = [
        'title' => 'Add Posts',
        'postTitle' => '',
        'body' => '',

      ];
      $this->view('posts/add', $data);
    }


  }

  public function edit($id)
  {
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
      // Sanitize POST array
      $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
      $data = [
        'postTitle' => trim($_POST['title']),
        'body' => trim($_POST['body']),
        'id' => $id,
        'title_err' => '',
        'body_err' => '',
      ];

      // Validate title
      if (empty($data['postTitle'])) {
        $data['title_err'] = 'Please enter title';
      }

      // Validate body
      if (empty($data['body'])) {
        $data['body_err'] = 'Please enter body text';
      }

      // Make sure no errors
      if (empty($data['title_err']) && empty($data['body_err'])) {
        //die('Success');
        // Validated
        if ($this->postModel->updatePost($data)) {
          flash('post_message', 'Post Updated');
          redirect('/posts');
        } else {
          die('Something went wrong');
        }
      } else {
        // load view with errors
        $this->view('posts/edit', $data);
      }

    } else {
      // Get existing post from model
      $post = $this->postModel->getPostById($id);
      // Check for owner

      if ($post->user_id != $_SESSION['user_id']) {
        redirect('/posts');
      }

      $data = [
        'id' => $id,
        'title' => 'Edit Post' . $post->title,
        'postTitle' => $post->title,
        'body' => $post->body,
      ];
      $this->view('posts/edit', $data);
    }


  }

  // post/show/3
  public function show($id){
    $post = $this->postModel->getPostById($id);
    $user = $this->userModel->getUserById($post->user_id);

    $data = [
      'title' =>$post->title,
      'post' => $post,
      'user' => $user
    ];

    $this->view('posts/show', $data);
  }

  public function delete($id)
  {
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
      // Get existing post from model
      $post = $this->postModel->getPostById($id);
      // Check for owner

      if ($post->user_id != $_SESSION['user_id']) {
        redirect('/posts');
      }
      if ($this->postModel->deletePost($id)) {
        flash('post_message', 'Post Removed');
        redirect('/posts');
      } else {
        die('Something went wrong');
      }
    } else {
      redirect('/posts');
    }
  }
}