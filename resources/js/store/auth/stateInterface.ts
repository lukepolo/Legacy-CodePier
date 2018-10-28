export interface AuthState {
  guards: {
    user: object;
    admin: object;
  };
  authAreaData: {
    name: string;
    email: string;
    password: string;
    confirm_password: string;
  };
}
