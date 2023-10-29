import { TUser } from "./types";
import md5 from 'md5-ts';

export default class Server {
    private HOST: string;
    private token: string | null;

    constructor(HOST: string) {
        this.HOST = HOST;
        this.token = null;
    }

    async request<T>(method: string, params: any = {}): Promise<T | null> {
        try {
            if (this.token) {
                params.token = this.token;
            }
            const str = Object.keys(params)
                .map(key => `${key}=${params[key]}`)
                .join('&');
            const res = await fetch(`${this.HOST}/?method=${method}&${str}`);
            const answer = await res.json();
            if (answer.result === 'ok') {
                return answer.data;
            }
            // обработать ошибку(6 пункт)
            //...
            return null;
        } catch (e) {
            return null;
        }
    }

    async login(login: string, hash: string, rnd: number): Promise<TUser | null> {
        const result = await this.request<TUser>(
            'login', 
            { login, hash, rnd }
        );
        if (result?.token) {
            this.token = result.token;
        }
        return result;
    }

    async logout(): Promise<boolean | null> {
        const result = await this.request<boolean>('logout');
        if (result) {
            this.token = null;
        }
        return result;
    }

    register(login: string, hash: string): Promise<TUser | null> {
        return this.request<TUser>('register', { login, hash });
    }

    getSkins(id: number, token: string): Promise<TUser | null> {
        return this.request<TUser>('getSkins', { id, token });
    }

    setSkin(id: number, token: string, skin: string): Promise<TUser | null> {
        return this.request<TUser>('setSkin', { id, token, skin });
    }
}