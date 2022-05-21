import tw from 'twin.macro';
import React, { useState } from 'react';
import useFlash from '@/plugins/useFlash';
import { ActionCreator } from 'easy-peasy';
import { StaticContext } from 'react-router';
import { FlashStore } from '@/state/flashes';
import Field from '@/components/elements/Field';
import Button from '@/components/elements/Button';
import { useFormikContext, withFormik } from 'formik';
import loginCheckpoint from '@/api/auth/loginCheckpoint';
import { Link, RouteComponentProps } from 'react-router-dom';
import LoginFormContainer from '@/components/auth/LoginFormContainer';

interface Values {
    code: string;
    recoveryCode: '',
}

type OwnProps = RouteComponentProps<Record<string, string | undefined>, StaticContext, { token?: string }>

type Props = OwnProps & {
    clearAndAddHttpError: ActionCreator<FlashStore['clearAndAddHttpError']['payload']>;
}

const LoginCheckpointContainer = () => {
    const { isSubmitting, setFieldValue } = useFormikContext<Values>();
    const [ isMissingDevice, setIsMissingDevice ] = useState(false);

    return (
        <LoginFormContainer title={'Device Checkpoint'} css={tw`w-full flex`}>
            <div css={tw`mt-6`}>
                <Field
                    light
                    name={isMissingDevice ? 'recoveryCode' : 'code'}
                    title={isMissingDevice ? 'Recovery Code' : 'Authentication Code'}
                    description={
                        isMissingDevice
                            ? 'Enter one of the recovery codes generated when you setup 2-Factor authentication on this account in order to continue.'
                            : 'Enter the two-factor token generated by your device.'
                    }
                    type={'text'}
                    autoComplete={'one-time-code'}
                    autoFocus
                />
            </div>
            <div css={tw`mt-6`}>
                <Button
                    size={'xlarge'}
                    type={'submit'}
                    disabled={isSubmitting}
                    isLoading={isSubmitting}
                >
                    Continue
                </Button>
            </div>
            <div css={tw`mt-6 text-center`}>
                <span
                    onClick={() => {
                        setFieldValue('code', '');
                        setFieldValue('recoveryCode', '');
                        setIsMissingDevice(s => !s);
                    }}
                    css={tw`cursor-pointer text-xs text-neutral-500 tracking-wide uppercase no-underline hover:text-neutral-700`}
                >
                    {!isMissingDevice ? 'I\'ve Lost My Device' : 'I Have My Device'}
                </span>
            </div>
            <div css={tw`mt-6 text-center`}>
                <Link
                    to={'/auth/login'}
                    css={tw`text-xs text-neutral-500 tracking-wide uppercase no-underline hover:text-neutral-700`}
                >
                    Return to Login
                </Link>
            </div>
        </LoginFormContainer>
    );
};

const EnhancedForm = withFormik<Props, Values>({
    handleSubmit: ({ code, recoveryCode }, { setSubmitting, props: { clearAndAddHttpError, location } }) => {
        loginCheckpoint(location.state?.token || '', code, recoveryCode)
            .then(response => {
                if (response.complete) {
                    // @ts-ignore
                    window.location = response.intended || '/';
                    return;
                }

                setSubmitting(false);
            })
            .catch(error => {
                console.error(error);
                setSubmitting(false);
                clearAndAddHttpError({ error });
            });
    },

    mapPropsToValues: () => ({
        code: '',
        recoveryCode: '',
    }),
})(LoginCheckpointContainer);

export default ({ history, location, ...props }: OwnProps) => {
    const { clearAndAddHttpError } = useFlash();

    if (!location.state?.token) {
        history.replace('/auth/login');

        return null;
    }

    return <EnhancedForm
        clearAndAddHttpError={clearAndAddHttpError}
        history={history}
        location={location}
        {...props}
    />;
};
