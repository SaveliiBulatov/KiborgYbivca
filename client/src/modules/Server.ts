import { TUser } from "./Types";

export default class Server {
    HOST: string;

    constructor(HOST: string) {
        this.HOST = HOST;
    }

    async request<T>(method: string, params: any): Promise<T | null> {
        try {
            const str = Object.keys(params)
                .map(key => `${key}=${params[key]}`)
                .join('&');
            const res = await fetch(`${this.HOST}/?method=${method}&${str}`);
            const answer = await res.json();
            if (answer.result === 'ok') {
                return answer.data;
            }
            // обработать ошибку
            //...
            return null;
        } catch (e) {
            return null;
        }
    }

    login(login: string, password: string): Promise<TUser | null> {
        return this.request<TUser>('login', { login, password });
    }

    async register(username: string,email: string , password: string): Promise<TUser | null> {
        try {
            const response = await this.request<TUser>('register', { username, email, password });
    
            if (response !== null) {
                return response;
            } else {
                return null;
            }
        } catch (error) {
            console.error("Ошибка при отправке запроса");
            return null;
        }
    }
}