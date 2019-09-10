import axios from 'axios';

export default {
    sendMail(title, fromName, fromEmail, to, body_txt, body_html) {
        return axios.post('/api/mail', {
            title,
            body_html,
            body_txt,
            to: to.split(';').map(item => item.trim()).filter(item => item !== ''),
            fromName,
            fromEmail
        }).then(response => {
            return response.data;
        });
    },
    getMail(id) {
        return axios.get('/api/mail/' + id).then(response => {
            return response.data;
        });
    }
}
