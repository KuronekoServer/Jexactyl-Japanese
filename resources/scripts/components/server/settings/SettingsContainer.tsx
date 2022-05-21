import React from 'react';
import tw from 'twin.macro';
import { formatIp } from '@/helpers';
import isEqual from 'react-fast-compare';
import { useStoreState } from 'easy-peasy';
import Can from '@/components/elements/Can';
import { ServerContext } from '@/state/server';
import Input from '@/components/elements/Input';
import Label from '@/components/elements/Label';
import { LinkButton } from '@/components/elements/Button';
import CopyOnClick from '@/components/elements/CopyOnClick';
import TitledGreyBox from '@/components/elements/TitledGreyBox';
import FlashMessageRender from '@/components/FlashMessageRender';
import ServerContentBlock from '@/components/elements/ServerContentBlock';
import RenameServerBox from '@/components/server/settings/RenameServerBox';
import ReinstallServerBox from '@/components/server/settings/ReinstallServerBox';

export default () => {
    const username = useStoreState(state => state.user.data!.username);
    const id = ServerContext.useStoreState(state => state.server.data!.id);
    const uuid = ServerContext.useStoreState(state => state.server.data!.uuid);
    const node = ServerContext.useStoreState(state => state.server.data!.node);
    const sftp = ServerContext.useStoreState(state => state.server.data!.sftpDetails, isEqual);

    return (
        <ServerContentBlock title={'Settings'}>
            <FlashMessageRender byKey={'settings'} css={tw`mb-4`}/>
            <div css={tw`md:flex`}>
                <div css={tw`w-full md:flex-1 md:mr-10`}>
                    <Can action={'file.sftp'}>
                        <TitledGreyBox title={'SFTP Details'} css={tw`mb-6 md:mb-10`}>
                            <div>
                                <Label>Server Address</Label>
                                <CopyOnClick text={`sftp://${formatIp(sftp.ip)}:${sftp.port}`}>
                                    <Input
                                        type={'text'}
                                        value={`sftp://${formatIp(sftp.ip)}:${sftp.port}`}
                                        readOnly
                                    />
                                </CopyOnClick>
                            </div>
                            <div css={tw`mt-6`}>
                                <Label>Username</Label>
                                <CopyOnClick text={`${username}.${id}`}>
                                    <Input
                                        type={'text'}
                                        value={`${username}.${id}`}
                                        readOnly
                                    />
                                </CopyOnClick>
                            </div>
                            <div css={tw`mt-6 flex items-center`}>
                                <div css={tw`flex-1`}>
                                    <div css={tw`border-l-4 border-cyan-500 p-3`}>
                                        <p css={tw`text-xs text-neutral-200`}>
                                            Your SFTP password is the same as the password you use to access this panel.
                                        </p>
                                    </div>
                                </div>
                                <div css={tw`ml-4`}>
                                    <LinkButton
                                        isSecondary
                                        href={`sftp://${username}.${id}@${formatIp(sftp.ip)}:${sftp.port}`}
                                    >
                                        Launch SFTP
                                    </LinkButton>
                                </div>
                            </div>
                        </TitledGreyBox>
                    </Can>
                    <TitledGreyBox title={'Debug Information'} css={tw`mb-6 md:mb-10`}>
                        <div css={tw`flex items-center justify-between text-sm`}>
                            <p>Node</p>
                            <code css={tw`font-mono bg-neutral-900 rounded py-1 px-2`}>{node}</code>
                        </div>
                        <CopyOnClick text={uuid}>
                            <div css={tw`flex items-center justify-between mt-2 text-sm`}>
                                <p>Server ID</p>
                                <code css={tw`font-mono bg-neutral-900 rounded py-1 px-2`}>{uuid}</code>
                            </div>
                        </CopyOnClick>
                    </TitledGreyBox>
                </div>
                <div css={tw`w-full mt-6 md:flex-1 md:mt-0`}>
                    <Can action={'settings.rename'}>
                        <div css={tw`mb-6 md:mb-10`}>
                            <RenameServerBox/>
                        </div>
                    </Can>
                    <Can action={'settings.reinstall'}>
                        <ReinstallServerBox/>
                    </Can>
                </div>
            </div>
        </ServerContentBlock>
    );
};
