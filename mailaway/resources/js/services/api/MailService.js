import axios from 'axios';

export default {
    sendMail () {
        console.log('todo implement');
    },
    getMail (id) {
        return axios.get('/api/mail/' + id).then(response => {
            return response.data;
        });
    }
}
