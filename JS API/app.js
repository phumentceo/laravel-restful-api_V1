   if(localStorage.getItem("token") == null){
      window.location.href = "login.html";
   }
   
   
    var url = "http://127.0.0.1:8000/api";
    var token = localStorage.getItem("token");


    const Message = (message) => {
        Toastify({
        text: `${message}`,
        duration: 2000,
        destination: "https://github.com/apvarun/toastify-js",
        newWindow: true,
        close: true,
        gravity: "top", // `top` or `bottom`
        position: "right", // `left`, `center` or `right`
        stopOnFocus: true, // Prevents dismissing of toast on hover
        style: {
            background: "linear-gradient(to right, #00b09b, #96c93d)",
        },
        onClick: function(){} // Callback after click
        }).showToast();
    }

    //arrow function es6
    const Posts = async () => {
        
        let response = await fetch(url + "/list",{
            method : "GET",
            headers : {
                ContentType : "application/json",
                Authorization : `Bearer ${token}`
            }
        });

        let data  = await response.json();
        let allPosts = data.posts;

        console.log(data.posts);

        let showPost = document.querySelector(".allPosts");

        showPost.innerHTML = "";
        allPosts.forEach(post => {
            let tr = `
                <tr class=" align-middle">
                    <td>${post.id}</td>
                    <td>
                        <img style="width:100px; height:100px;" src="${post.image}" />
                    </td>
                    <td>${post.title}</td>
                    <td>${post.des}</td>
                    <td class=" text-center">
                        <button onclick="EditPost(${post.id})" data-bs-toggle="modal" data-bs-target="#modalUpdatePost"  class=" btn btn-info btn-sm">edit</button>
                        <button onclick="DeletePost(${post.id})" class=" btn btn-danger btn-sm">delete</button>
                    </td>
                </tr>
            `;

            showPost.innerHTML  += tr; 
        });

        
    }

    Posts();

    const StorePost = async () => {
        let payload = new FormData(document.getElementById("formCreatePost"));
        let response = await fetch(url+"/store",{
            method : "POST",
            headers : {
                ContentType : "application/json",
                Authorization : `Bearer ${token}`
            },
            body : payload 
        });

        if(response.ok){
            $("#modalCreatPost").modal("hide");
            $("#formCreatePost").trigger("reset");
            Message("Post created successfully");
            Posts();
        }

    }


    const DeletePost = async (id) => {
        if(confirm("Do you want to delete this?")){
            let response = await fetch(url+`/delete/${id}`,{
                method : "POST",
                headers : {
                    ContentType : "application/json",
                    Authorization : `Bearer ${token}`
                },
               
            });
    
            if(response.ok){
                Message("Post deleted successfully");
                Posts();
            }
        }
        
    }


    const EditPost = async (id) => {
        let response = await fetch(url+`/edit/${id}`,{
            method : "GET",
            headers : {
                ContentType : "application/json",
                Authorization : `Bearer ${token}`
            },
           
        });

        let data = await response.json();

        let post = data.post;

        console.log(post);

        document.querySelector(".post_id").value = post.id;
        document.querySelector(".title").value = post.title;
        document.querySelector(".description").innerText = post.des;
        document.querySelector(".show-image").innerHTML = `
           
           <img src='${post.image}' style="width:300px;" />
        `;
    }


    const UpdatePost = async () => {
        let payload = new FormData(document.getElementById("formUpdatePost"));
        let id = payload.get("id");
       
        let response = await fetch(url+`/update/${id}`,{
            method : "POST",
            headers : {
                ContentType : "application/json",
                Authorization : `Bearer ${token}`
            },
            body : payload 
        });

        if (response.ok) {
            $("#modalUpdatePost").modal("hide");
            $("#formUpdatePost").trigger("reset");
            Message("Post updated successfully");
            Posts();
        }
        
    }


    const Logout = async () => {

        if(confirm('Are you sure you want to logout?')){
            let response = await fetch(url+`/logout`,{
                method : "GET",
                headers : {
                    ContentType : "application/json",
                    Authorization : `Bearer ${token}`
                }
            });
            
            if(response.ok){
                localStorage.removeItem("token");
                window.location.href = "login.html";
            }

        }  
    }


   

  
