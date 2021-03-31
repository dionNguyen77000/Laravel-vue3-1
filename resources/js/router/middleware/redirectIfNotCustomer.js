export default function redirectIfNotCustomer({next, store}) {
    console.log("🚀 ~ file: redirectNoCust.js ~ line 3 ~ auth ~ store.getters['auth/getAuth']", store.getters['auth/getAuth'])

    if(!store.getters['auth/getAuth'].isCustomer) {
        return next({
            name: 'Home'
        })
    }

    return next();
}