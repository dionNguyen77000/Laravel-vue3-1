export default function redirectIfNotFourthLevelUser({next, store}) {
    // console.log("🚀 ~ file: redirectNoCust.js ~ line 3 ~ auth ~ store.getters['auth/getAuth']", store.getters['auth/getAuth'])

    if(!store.getters['auth/getAuth'].isFourthLevelUser) {
        return next({
            name: 'Dashboard'
        })
    }

    return next();
}