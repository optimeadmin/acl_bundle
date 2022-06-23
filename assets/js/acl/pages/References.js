import React from 'react'
import { Nav, Tab } from 'react-bootstrap'
import TypedReferences from './section/TypedReferences'
import useReferences from '../hooks/useReferences'
import LoadingIcon from '../components/LoadingIcon'

const References = () => {
    const existentReferences = useReferences('existent')
    const newsReferences = useReferences('news')
    const hiddenReferences = useReferences('hidden')
    const isFetching = existentReferences.isFetching

    const defaultTab = existentReferences.serverCount > 0 ? 'persisted' : 'news'

    if (existentReferences.isLoading) {
        return <h3>Loading...</h3>
    }

    return (
        <div className={`acl-page-container ${isFetching ? 'is-loading' : ''}`}>
            <div className="d-flex gap-2 align-items-center border-bottom pb-3">
                <h3 className="m-0">Controllers Configuration</h3>
                <LoadingIcon isLoading={isFetching} size="md" className="ms-2" />
            </div>

            <section className="mt-4">

                <Tab.Container defaultActiveKey={defaultTab} transition={false}>
                    <Nav variant="pills">
                        {existentReferences.serverCount > 0 && (
                            <Nav.Item>
                                <Nav.Link role="button" eventKey="persisted">Persisted</Nav.Link>
                            </Nav.Item>
                        )}
                        {newsReferences.serverCount > 0 && (
                            <Nav.Item>
                                <Nav.Link role="button" eventKey="news">News</Nav.Link>
                            </Nav.Item>
                        )}
                        {hiddenReferences.serverCount > 0 && (
                            <Nav.Item>
                                <Nav.Link role="button" eventKey="hidden">Hidden</Nav.Link>
                            </Nav.Item>
                        )}
                    </Nav>

                    <hr/>

                    <Tab.Content className="mt-5">
                        {existentReferences.serverCount > 0 && (
                            <Tab.Pane eventKey="persisted" unmountOnExit={true}>
                                <TypedReferences
                                    title="Persisted"
                                    {...existentReferences}
                                />
                            </Tab.Pane>
                        )}
                        {newsReferences.serverCount > 0 && (
                            <Tab.Pane eventKey="news" unmountOnExit={true}>
                                <TypedReferences
                                    title="News"
                                    {...newsReferences}
                                />
                            </Tab.Pane>
                        )}
                        {hiddenReferences.serverCount > 0 && (
                            <Tab.Pane eventKey="hidden" unmountOnExit={true}>
                                <TypedReferences
                                    title="Hidden"
                                    {...hiddenReferences}
                                    showHide={false}
                                />
                            </Tab.Pane>
                        )}
                    </Tab.Content>
                </Tab.Container>

            </section>
        </div>
    )
}

export default References
