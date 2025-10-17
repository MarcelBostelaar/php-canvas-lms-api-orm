{
    "filename": "SubmissionFeedback.php",
    "fields": [
        {
            "type": "string",
            "name": "feedbackGiver"
        },
        {
            "type": "string",
            "name": "comment"
        },
        {
            "type": "DateTime",
            "name": "date"
        }
    ],
    "fieldsNullable": [],
    "plurals": [
        "SubmissionFeedbacks"
    ]
}array(
    0: Stmt_Namespace(
        name: Name(
            name: CanvasApiLibrary\Models
        )
        stmts: array(
            0: Stmt_Use(
                type: TYPE_NORMAL (1)
                uses: array(
                    0: UseItem(
                        type: TYPE_UNKNOWN (0)
                        name: Name(
                            name: CanvasApiLibrary\Models\Utility\AbstractCanvasPopulatedModel
                        )
                        alias: null
                    )
                )
            )
            1: Stmt_Class(
                attrGroups: array(
                )
                flags: FINAL (32)
                name: Identifier(
                    name: SubmissionFeedback
                )
                extends: Name_FullyQualified(
                    name: CanvasApiLibrary\Models\Utility\AbstractCanvasPopulatedModel
                )
                implements: array(
                )
                stmts: array(
                    0: Stmt_Property(
                        attrGroups: array(
                        )
                        flags: PROTECTED | STATIC (10)
                        type: Identifier(
                            name: array
                        )
                        props: array(
                            0: PropertyItem(
                                name: VarLikeIdentifier(
                                    name: properties
                                )
                                default: Expr_Array(
                                    items: array(
                                        0: ArrayItem(
                                            key: null
                                            value: Expr_Array(
                                                items: array(
                                                    0: ArrayItem(
                                                        key: null
                                                        value: Scalar_String(
                                                            value: string
                                                        )
                                                        byRef: false
                                                        unpack: false
                                                    )
                                                    1: ArrayItem(
                                                        key: null
                                                        value: Scalar_String(
                                                            value: feedbackGiver
                                                        )
                                                        byRef: false
                                                        unpack: false
                                                    )
                                                )
                                            )
                                            byRef: false
                                            unpack: false
                                        )
                                        1: ArrayItem(
                                            key: null
                                            value: Expr_Array(
                                                items: array(
                                                    0: ArrayItem(
                                                        key: null
                                                        value: Scalar_String(
                                                            value: string
                                                        )
                                                        byRef: false
                                                        unpack: false
                                                    )
                                                    1: ArrayItem(
                                                        key: null
                                                        value: Scalar_String(
                                                            value: comment
                                                        )
                                                        byRef: false
                                                        unpack: false
                                                    )
                                                )
                                            )
                                            byRef: false
                                            unpack: false
                                        )
                                        2: ArrayItem(
                                            key: null
                                            value: Expr_Array(
                                                items: array(
                                                    0: ArrayItem(
                                                        key: null
                                                        value: Expr_ClassConstFetch(
                                                            class: Name_FullyQualified(
                                                                name: DateTime
                                                            )
                                                            name: Identifier(
                                                                name: class
                                                            )
                                                        )
                                                        byRef: false
                                                        unpack: false
                                                    )
                                                    1: ArrayItem(
                                                        key: null
                                                        value: Scalar_String(
                                                            value: date
                                                        )
                                                        byRef: false
                                                        unpack: false
                                                    )
                                                )
                                            )
                                            byRef: false
                                            unpack: false
                                        )
                                    )
                                )
                            )
                        )
                        hooks: array(
                        )
                    )
                    1: Stmt_Property(
                        attrGroups: array(
                        )
                        flags: PUBLIC | STATIC (9)
                        type: Identifier(
                            name: array
                        )
                        props: array(
                            0: PropertyItem(
                                name: VarLikeIdentifier(
                                    name: plurals
                                )
                                default: Expr_Array(
                                    items: array(
                                        0: ArrayItem(
                                            key: null
                                            value: Scalar_String(
                                                value: SubmissionFeedbacks
                                            )
                                            byRef: false
                                            unpack: false
                                        )
                                    )
                                )
                            )
                        )
                        hooks: array(
                        )
                    )
                )
            )
        )
    )
)