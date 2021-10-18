export default function redirectIfNotThirdLevelUser({next, store}) {
    // console.log("🚀 ~ file: redirectNoCust.js ~ line 3 ~ auth ~ store.getters['auth/getAuth']", store.getters['auth/getAuth'])

    if(!store.getters['auth/getAuth'].isThirdLevelUser) {
        return next({
            name: 'Dashboard'
        })
    }

    return next();
}