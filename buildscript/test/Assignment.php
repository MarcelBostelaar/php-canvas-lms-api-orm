{
    "filename": "Assignment.php",
    "fields": [
        {
            "type": "CanvasApiLibrary\\Models\\GroupCategory",
            "name": "group"
        }
    ],
    "fieldsNullable": [],
    "plurals": [
        "Assignments"
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
                    name: Assignment
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
                        type: null
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
                                                        value: Expr_ClassConstFetch(
                                                            class: Name_FullyQualified(
                                                                name: CanvasApiLibrary\Models\GroupCategory
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
                                                            value: group
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
                                                value: Assignments
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