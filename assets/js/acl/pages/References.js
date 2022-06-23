import React from 'react'
import { Nav, Tab } from 'react-bootstrap'
import TypedReferences from './section/TypedReferences'
import useReferences from '../hooks/useReferences'

const References = () => {
    const existentReferences = useReferences('existent')
    const newsReferences = useReferences('news')
    const hiddenReferences = useReferences('hidden')

    const defaultTab = existentReferences.serverCount > 0 ? 'persisted' : 'news'

    if (existentReferences.isLoading) {
        return <h3>Loading...</h3>
    }

    return (
        <div>
            <div className="d-flex gap-2 align-items-center justify-content-between border-bottom pb-3">
                <h3 className="m-0">Controllers Configuration</h3>
            </div>

            <section className="mt-4">

                <Tab.Container defaultActiveKey={defaultTab} transition={false}>
                    <Nav variant="pills">
                        {existentReferences.count > 0 && (
                            <Nav.Item>
                                <Nav.Link role="button" eventKey="persisted">Persisted</Nav.Link>
                            </Nav.Item>
                        )}
                        {newsReferences.count > 0 && (
                            <Nav.Item>
                                <Nav.Link role="button" eventKey="news">News</Nav.Link>
                            </Nav.Item>
                        )}
                        {hiddenReferences.count > 0 && (
                            <Nav.Item>
                                <Nav.Link role="button" eventKey="hidden">Hidden</Nav.Link>
                            </Nav.Item>
                        )}
                    </Nav>

                    <hr/>

                    <Tab.Content className="mt-5">
                        {existentReferences.count > 0 && (
                            <Tab.Pane eventKey="persisted" unmountOnExit={true}>
                                <TypedReferences
                                    title="Persisted"
                                    {...existentReferences}
                                />
                            </Tab.Pane>
                        )}
                        {newsReferences.count > 0 && (
                            <Tab.Pane eventKey="news" unmountOnExit={true}>
                                <TypedReferences
                                    title="News"
                                    {...newsReferences}
                                />
                            </Tab.Pane>
                        )}
                        {hiddenReferences.count > 0 && (
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
