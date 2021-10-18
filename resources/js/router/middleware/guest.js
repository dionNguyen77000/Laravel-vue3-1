export default function guest({next,store}) {
    // console.log("🚀 ~ file: guest.js ~ line 3 ~ auth ~ guest.getters['auth/getAuth']", store.getters['auth/getAuth'])
   
    if(store.getters['auth/getAuth'].loggedIn) {
        return next({
            name: 'Dashboard'
        })
    }

    return next();
}