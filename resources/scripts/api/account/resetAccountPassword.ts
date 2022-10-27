import http from '@/api/http';

export default (): Promise<void> => {
    return new Promise((resolve, reject) => {
        http.put('/api/client/account/password')
            .then(() => resolve())
            .catch(reject);
    });
};
