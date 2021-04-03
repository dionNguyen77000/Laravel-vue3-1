export default function auth({next,store}) {
    // console.log("🚀 ~ file: auth.js ~ line 3 ~ auth ~ store.getters['auth/getAuth']", store.getters['auth/getAuth'])
   
    if(!store.getters['auth/getAuth'].loggedIn) {
        return next({
            name: 'Login'
        })
    }

    console.log('user login', store.getters['auth/getAuth'].loggedIn);

    return next();
}