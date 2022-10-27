import tw from 'twin.macro';
import React, { useState } from 'react';
import useFlash from '@/plugins/useFlash';
import { useStoreState } from 'easy-peasy';
import Code from '@/components/elements/Code';
import http, { httpErrorToHuman } from '@/api/http';
import { Button } from '@/components/elements/button/index';
import SpinnerOverlay from '@/components/elements/SpinnerOverlay';
import resetAccountPassword from '@/api/account/resetAccountPassword';

export default () => {
    const [loading, setLoading] = useState(false);
    const { clearFlashes, addFlash } = useFlash();
    const user = useStoreState((state) => state.user.data);

    if (!user) return null;

    const submit = () => {
        setLoading(true);
        clearFlashes('account:password');

        resetAccountPassword()
            .then(() => {
                http.post('/auth/logout').finally(() => {
                    // @ts-expect-error this is valid
                    window.location = '/';
                });
                setLoading(false);
            })
            .catch((error) =>
                addFlash({
                    key: 'account:password',
                    type: 'error',
                    title: 'Error',
                    message: httpErrorToHuman(error),
                })
            )
            .then(() => {
                addFlash({
                    type: 'success',
                    title: 'An email has been sent!',
                    message: 'We have sent a recovery email to ' + user.email,
                });
            });
    };

    return (
        <React.Fragment>
            <SpinnerOverlay size={'large'} visible={loading} />
            <p className={'text-sm'}>
                Want to reset your password? Click the button below in order to recieve an email prompting you to change
                your password.
            </p>
            <p className={'text-sm mt-1 italic'}>
                This email will be sent to: <Code>{user.email}</Code>
            </p>
            <div css={tw`mt-6`}>
                <Button disabled={loading} onClick={submit}>
                    Send Reset Link
                </Button>
            </div>
        </React.Fragment>
    );
};
