{
    "filename": "GroupCategory.php",
    "fields": [],
    "fieldsNullable": [],
    "plurals": [
        "GroupCategories"
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
                    name: GroupCategory
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
                                                value: GroupCategories
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