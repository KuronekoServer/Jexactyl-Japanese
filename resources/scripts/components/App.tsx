import React from 'react';
import { store } from '@/state';
import { StoreProvider } from 'easy-peasy';
import { hot } from 'react-hot-loader/root';
import { history } from '@/components/history';
import { SiteSettings } from '@/state/settings';
import ServerRouter from '@/routers/ServerRouter';
import { setupInterceptors } from '@/api/interceptors';
import DashboardRouter from '@/routers/DashboardRouter';
import { Route, Router, Switch } from 'react-router-dom';
import ProgressBar from '@/components/elements/ProgressBar';
import { NotFound } from '@/components/elements/ScreenBlock';
import GlobalStylesheet from '@/assets/css/GlobalStylesheet';
import AuthenticationRouter from '@/routers/AuthenticationRouter';
import tw, { GlobalStyles as TailwindGlobalStyles } from 'twin.macro';

interface ExtendedWindow extends Window {
    SiteConfiguration?: SiteSettings;
    PterodactylUser?: {
        uuid: string;
        username: string;
        email: string;
        /* eslint-disable camelcase */
        root_admin: boolean;
        use_totp: boolean;
        language: string;
        updated_at: string;
        created_at: string;
        /* eslint-enable camelcase */
    };
}

setupInterceptors(history);

const App = () => {
    const { PterodactylUser, SiteConfiguration } = (window as ExtendedWindow);
    if (PterodactylUser && !store.getState().user.data) {
        store.getActions().user.setUserData({
            uuid: PterodactylUser.uuid,
            username: PterodactylUser.username,
            email: PterodactylUser.email,
            language: PterodactylUser.language,
            rootAdmin: PterodactylUser.root_admin,
            useTotp: PterodactylUser.use_totp,
            createdAt: new Date(PterodactylUser.created_at),
            updatedAt: new Date(PterodactylUser.updated_at),
        });
    }

    if (!store.getState().settings.data) {
        store.getActions().settings.setSettings(SiteConfiguration!);
    }

    return (
        <>
            <GlobalStylesheet/>
            <TailwindGlobalStyles/>
            <StoreProvider store={store}>
                <ProgressBar/>
                <div css={tw`mx-auto w-auto`}>
                    <Router history={history}>
                        <Switch>
                            <Route path="/server/:id" component={ServerRouter}/>
                            <Route path="/auth" component={AuthenticationRouter}/>
                            <Route path="/" component={DashboardRouter}/>
                            <Route path={'*'} component={NotFound}/>
                        </Switch>
                    </Router>
                </div>
            </StoreProvider>
        </>
    );
};

export default hot(App);
