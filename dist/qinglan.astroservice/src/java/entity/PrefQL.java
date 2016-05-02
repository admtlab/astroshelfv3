/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
package entity;

import java.io.Serializable;
import java.util.Collection;
import javax.persistence.*;
import javax.validation.constraints.NotNull;
import javax.validation.constraints.Size;
import javax.xml.bind.annotation.XmlRootElement;
import javax.xml.bind.annotation.XmlTransient;

/**
 *
 * @author roxy
 */
@Entity
@Table(name = "prefQL")
@XmlRootElement
@NamedQueries({
    @NamedQuery(name = "PrefQL.findAll", query = "SELECT p FROM PrefQL p"),
    @NamedQuery(name = "PrefQL.findByPrefqlId", query = "SELECT p FROM PrefQL p WHERE p.prefqlId = :prefqlId"),
    @NamedQuery(name = "PrefQL.findByPredicateLeft", query = "SELECT p FROM PrefQL p WHERE p.predicateLeft = :predicateLeft"),
    @NamedQuery(name = "PrefQL.findByPredicateRight", query = "SELECT p FROM PrefQL p WHERE p.predicateRight = :predicateRight"),
    @NamedQuery(name = "PrefQL.findByIntensity", query = "SELECT p FROM PrefQL p WHERE p.intensity = :intensity"),
    @NamedQuery(name = "PrefQL.findByPrefName", query = "SELECT p FROM PrefQL p WHERE p.prefName = :prefName")})
public class PrefQL implements Serializable {
    private static final long serialVersionUID = 1L;
    @Id
    @GeneratedValue(strategy = GenerationType.IDENTITY)
    @Basic(optional = false)
    @NotNull
    @Column(name = "prefql_id")
    private Long prefqlId;
    @Basic(optional = false)
    @NotNull
    @Size(min = 1, max = 100)
    @Column(name = "predicate_left")
    private String predicateLeft;
    @Basic(optional = false)
    @NotNull
    @Size(min = 1, max = 100)
    @Column(name = "predicate_right")
    private String predicateRight;
    @Basic(optional = false)
    @NotNull
    @Column(name = "intensity")
    private double intensity;
    @Basic(optional = false)
    @NotNull
    @Size(min = 1, max = 20)
    @Column(name = "prefName")
    private String prefName;
    @ManyToMany(mappedBy = "prefQLCollection")
    private Collection<PrefAnnotation> prefAnnotationCollection;
    @JoinColumn(name = "userId", referencedColumnName = "user_id")
    @ManyToOne(optional = false)
    private User userId;

    public PrefQL() {
    }

    public PrefQL(Long prefqlId) {
        this.prefqlId = prefqlId;
    }

    public PrefQL(Long prefqlId, String predicateLeft, String predicateRight, double intensity, String prefName) {
        this.prefqlId = prefqlId;
        this.predicateLeft = predicateLeft;
        this.predicateRight = predicateRight;
        this.intensity = intensity;
        this.prefName = prefName;
    }

    public Long getPrefqlId() {
        return prefqlId;
    }

    public void setPrefqlId(Long prefqlId) {
        this.prefqlId = prefqlId;
    }

    public String getPredicateLeft() {
        return predicateLeft;
    }

    public void setPredicateLeft(String predicateLeft) {
        this.predicateLeft = predicateLeft;
    }

    public String getPredicateRight() {
        return predicateRight;
    }

    public void setPredicateRight(String predicateRight) {
        this.predicateRight = predicateRight;
    }

    public double getIntensity() {
        return intensity;
    }

    public void setIntensity(double intensity) {
        this.intensity = intensity;
    }

    public String getPrefName() {
        return prefName;
    }

    public void setPrefName(String prefName) {
        this.prefName = prefName;
    }

    @XmlTransient
    public Collection<PrefAnnotation> getPrefAnnotationCollection() {
        return prefAnnotationCollection;
    }

    public void setPrefAnnotationCollection(Collection<PrefAnnotation> prefAnnotationCollection) {
        this.prefAnnotationCollection = prefAnnotationCollection;
    }

    public User getUserId() {
        return userId;
    }

    public void setUserId(User userId) {
        this.userId = userId;
    }

    @Override
    public int hashCode() {
        int hash = 0;
        hash += (prefqlId != null ? prefqlId.hashCode() : 0);
        return hash;
    }

    @Override
    public boolean equals(Object object) {
        // TODO: Warning - this method won't work in the case the id fields are not set
        if (!(object instanceof PrefQL)) {
            return false;
        }
        PrefQL other = (PrefQL) object;
        if ((this.prefqlId == null && other.prefqlId != null) || (this.prefqlId != null && !this.prefqlId.equals(other.prefqlId))) {
            return false;
        }
        return true;
    }

    @Override
    public String toString() {
        return "entity.PrefQL[ prefqlId=" + prefqlId + " ]";
    }
    
}
