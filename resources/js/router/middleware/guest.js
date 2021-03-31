export default function guest({next,store}) {
    console.log("🚀 ~ file: guest.js ~ line 3 ~ auth ~ guest.getters['auth/getAuth']", store.getters['auth/getAuth'])
    console.log("🚀 ~ file: guest.js user logged in", store.getters['auth/getAuth'].loggedIn)
   
    if(store.getters['auth/getAuth'].loggedIn) {
        console.log('I am here')
        return next({
            name: 'Home'
        })
    }

    return next();
}